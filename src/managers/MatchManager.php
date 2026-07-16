<?php

declare(strict_types=1);

namespace App\Managers;

final class MatchManager extends AbstractManager
{
    public function findAll(): array
    {
        $sql = 'SELECT m.*,
                       t1.name AS team1_name,
                       t1.flag AS team1_flag,
                       t2.name AS team2_name,
                       t2.flag AS team2_flag
                FROM matches m
                INNER JOIN teams t1 ON m.team1_id = t1.team_id
                INNER JOIN teams t2 ON m.team2_id = t2.team_id
                ORDER BY m.match_date ASC';

        return $this->pdo->query($sql)->fetchAll();
    }

    public function findUpcoming(int $limit = 20): array
    {
        $sql = 'SELECT m.*,
                       t1.name AS team1_name,
                       t1.flag AS team1_flag,
                       t2.name AS team2_name,
                       t2.flag AS team2_flag
                FROM matches m
                INNER JOIN teams t1 ON m.team1_id = t1.team_id
                INNER JOIN teams t2 ON m.team2_id = t2.team_id
                WHERE m.status = "scheduled"
                  AND m.match_date > NOW()
                ORDER BY m.match_date ASC
                LIMIT :limit';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTeams(): array
    {
        return $this->pdo->query('SELECT * FROM teams ORDER BY name ASC')->fetchAll();
    }

    public function searchTeamsWithMatches(string $query): array
    {
        $teamStmt = $this->pdo->prepare(
            'SELECT *
             FROM teams
             WHERE name LIKE :query
                OR flag IN (
                    SELECT flag
                    FROM teams
                    WHERE name LIKE :query_with_same_flag
                      AND flag IS NOT NULL
                      AND flag <> ""
                )
             ORDER BY name ASC'
        );
        $teamStmt->execute([
            'query' => '%' . $query . '%',
            'query_with_same_flag' => '%' . $query . '%',
        ]);
        $teams = $teamStmt->fetchAll();

        $results = [];
        $processedFlags = [];

        foreach ($teams as $team) {
            $flag = (string) ($team['flag'] ?? '');

            if ($flag !== '' && in_array($flag, $processedFlags, true)) {
                continue;
            }

            if ($flag !== '') {
                $processedFlags[] = $flag;
            }

            $teamId = (int) $team['team_id'];
            $relatedTeamIds = [$teamId];

            if ($flag !== '') {
                $relatedStmt = $this->pdo->prepare(
                    'SELECT team_id FROM teams WHERE flag = :flag ORDER BY team_id ASC'
                );
                $relatedStmt->execute(['flag' => $flag]);
                $relatedTeamIds = array_map(
                    static fn (array $row): int => (int) $row['team_id'],
                    $relatedStmt->fetchAll()
                );
            }

            $placeholders = [];
            $params = [];

            foreach ($relatedTeamIds as $index => $relatedTeamId) {
                $placeholder = ':team_id_' . $index;
                $placeholders[] = $placeholder;
                $params[$placeholder] = $relatedTeamId;
            }

            $idsSql = implode(', ', $placeholders);
            $matchStmt = $this->pdo->prepare(
                'SELECT m.*,
                        t1.name AS team1_name,
                        t1.flag AS team1_flag,
                        t2.name AS team2_name,
                        t2.flag AS team2_flag,
                        CASE WHEN m.team1_id IN (' . $idsSql . ') THEN t2.name ELSE t1.name END AS opponent_name,
                        CASE WHEN m.team1_id IN (' . $idsSql . ') THEN t2.flag ELSE t1.flag END AS opponent_flag,
                        CASE WHEN m.team1_id IN (' . $idsSql . ') THEN 1 ELSE 2 END AS searched_team_side,
                        m.match_date > NOW() AND m.status = "scheduled" AS prediction_open
                 FROM matches m
                 INNER JOIN teams t1 ON m.team1_id = t1.team_id
                 INNER JOIN teams t2 ON m.team2_id = t2.team_id
                 WHERE m.team1_id IN (' . $idsSql . ') OR m.team2_id IN (' . $idsSql . ')
                 ORDER BY m.match_date ASC'
            );
            $matchStmt->execute($params);
            $matches = $matchStmt->fetchAll();

            $upcoming = [];
            $past = [];
            $stats = [
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
            ];

            foreach ($matches as $match) {
                $isUpcoming = (int) $match['prediction_open'] === 1;

                if ($isUpcoming) {
                    $upcoming[] = $match;
                    continue;
                }

                $past[] = $match;

                if ($match['status'] !== 'finished' || empty($match['result'])) {
                    continue;
                }

                if ($match['result'] === 'N') {
                    $stats['draws']++;
                    continue;
                }

                $searchedTeamWon = ((int) $match['searched_team_side'] === 1 && $match['result'] === '1')
                    || ((int) $match['searched_team_side'] === 2 && $match['result'] === '2');

                if ($searchedTeamWon) {
                    $stats['wins']++;
                } else {
                    $stats['losses']++;
                }
            }

            $results[] = [
                'team' => $team,
                'upcoming' => $upcoming,
                'past' => $past,
                'stats' => $stats,
            ];
        }

        return $results;
    }

    public function create(int $team1Id, int $team2Id, string $matchDate, string $description, string $apiMatchId = ''): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO matches (team1_id, team2_id, match_date, description, status, api_provider, api_match_id)
             VALUES (:team1_id, :team2_id, :match_date, :description, "scheduled", "football-data", :api_match_id)'
        );

        $stmt->execute([
            'team1_id' => $team1Id,
            'team2_id' => $team2Id,
            'match_date' => $matchDate,
            'description' => $description !== '' ? $description : null,
            'api_match_id' => $apiMatchId !== '' ? $apiMatchId : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findMatchesDueForResultSync(int $limit): array
    {
        $sql = 'SELECT m.*,
                       t1.name AS team1_name,
                       t2.name AS team2_name,
                       CASE
                           WHEN TIMESTAMPDIFF(MINUTE, m.match_date, NOW()) >= 120 AND m.result_check_120_at IS NULL THEN 120
                           WHEN TIMESTAMPDIFF(MINUTE, m.match_date, NOW()) >= 150 AND m.result_check_150_at IS NULL THEN 150
                           WHEN TIMESTAMPDIFF(MINUTE, m.match_date, NOW()) >= 180 AND m.result_check_180_at IS NULL THEN 180
                           ELSE NULL
                       END AS result_sync_checkpoint
                FROM matches m
                INNER JOIN teams t1 ON m.team1_id = t1.team_id
                INNER JOIN teams t2 ON m.team2_id = t2.team_id
                WHERE m.status = "scheduled"
                  AND m.match_date <= DATE_SUB(NOW(), INTERVAL 120 MINUTE)
                  AND (
                      (TIMESTAMPDIFF(MINUTE, m.match_date, NOW()) >= 120 AND m.result_check_120_at IS NULL)
                      OR (TIMESTAMPDIFF(MINUTE, m.match_date, NOW()) >= 150 AND m.result_check_150_at IS NULL)
                      OR (TIMESTAMPDIFF(MINUTE, m.match_date, NOW()) >= 180 AND m.result_check_180_at IS NULL)
                  )
                ORDER BY m.match_date ASC
                LIMIT :limit';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findPastScheduledMatchesForResultSync(int $limit): array
    {
        $sql = 'SELECT m.*,
                       t1.name AS team1_name,
                       t2.name AS team2_name,
                       CASE
                           WHEN TIMESTAMPDIFF(MINUTE, m.match_date, NOW()) >= 180 AND m.result_check_180_at IS NULL THEN 180
                           WHEN TIMESTAMPDIFF(MINUTE, m.match_date, NOW()) >= 150 AND m.result_check_150_at IS NULL THEN 150
                           WHEN TIMESTAMPDIFF(MINUTE, m.match_date, NOW()) >= 120 AND m.result_check_120_at IS NULL THEN 120
                           ELSE NULL
                       END AS result_sync_checkpoint
                FROM matches m
                INNER JOIN teams t1 ON m.team1_id = t1.team_id
                INNER JOIN teams t2 ON m.team2_id = t2.team_id
                WHERE m.status = "scheduled"
                  AND m.match_date <= DATE_SUB(NOW(), INTERVAL 120 MINUTE)
                ORDER BY m.match_date ASC
                LIMIT :limit';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function markResultCheckAttempt(int $matchId, int $checkpoint): void
    {
        $columns = [
            120 => 'result_check_120_at',
            150 => 'result_check_150_at',
            180 => 'result_check_180_at',
        ];

        if (!isset($columns[$checkpoint])) {
            throw new \InvalidArgumentException('Checkpoint de synchronisation invalide.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE matches SET ' . $columns[$checkpoint] . ' = NOW() WHERE match_id = :match_id'
        );
        $stmt->execute(['match_id' => $matchId]);
    }

    public function setResultSyncError(int $matchId, ?string $message): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE matches SET result_sync_error = :message WHERE match_id = :match_id'
        );
        $stmt->execute([
            'message' => $message,
            'match_id' => $matchId,
        ]);
    }

    public function setResult(int $matchId, int $resultTeam1, int $resultTeam2, ?string $apiMatchId = null): void
    {
        $result = 'N';
        if ($resultTeam1 > $resultTeam2) {
            $result = '1';
        } elseif ($resultTeam2 > $resultTeam1) {
            $result = '2';
        }

        $stmt = $this->pdo->prepare(
            'UPDATE matches
             SET result_team1 = :result_team1,
                 result_team2 = :result_team2,
                 result = :result,
                 status = "finished",
                 api_match_id = COALESCE(:api_match_id, api_match_id),
                 result_sync_error = NULL
             WHERE match_id = :match_id'
        );

        $stmt->execute([
            'result_team1' => $resultTeam1,
            'result_team2' => $resultTeam2,
            'result' => $result,
            'api_match_id' => $apiMatchId,
            'match_id' => $matchId,
        ]);
    }

    public function setResultFromOutcome(int $matchId, string $result): void
    {
        $scores = [
            '1' => [1, 0],
            'N' => [0, 0],
            '2' => [0, 1],
        ];

        if (!isset($scores[$result])) {
            throw new \InvalidArgumentException('Résultat invalide.');
        }

        [$resultTeam1, $resultTeam2] = $scores[$result];

        $stmt = $this->pdo->prepare(
            'UPDATE matches
             SET result_team1 = :result_team1,
                 result_team2 = :result_team2,
                 result = :result,
                 status = "finished"
             WHERE match_id = :match_id'
        );

        $stmt->execute([
            'result_team1' => $resultTeam1,
            'result_team2' => $resultTeam2,
            'result' => $result,
            'match_id' => $matchId,
        ]);
    }

    public function delete(int $matchId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM matches WHERE match_id = :match_id');
        $stmt->execute(['match_id' => $matchId]);
    }
}
