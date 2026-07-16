<?php

declare(strict_types=1);

namespace App\Managers;

final class PredictionManager extends AbstractManager
{
    public function findByUser(int $userId): array
    {
        $sql = 'SELECT p.*,
                       m.match_date,
                       m.result,
                       t1.name AS team1_name,
                       t1.flag AS team1_flag,
                       t2.name AS team2_name,
                       t2.flag AS team2_flag
                FROM predictions p
                INNER JOIN matches m ON p.match_id = m.match_id
                INNER JOIN teams t1 ON m.team1_id = t1.team_id
                INNER JOIN teams t2 ON m.team2_id = t2.team_id
                WHERE p.user_id = :user_id
                ORDER BY m.match_date ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function saveOrUpdate(int $userId, int $matchId, string $predictionResult): void
    {
        $checkMatch = $this->pdo->prepare(
            'SELECT status, match_date, match_date <= NOW() AS has_started
             FROM matches
             WHERE match_id = :match_id
             LIMIT 1'
        );
        $checkMatch->execute(['match_id' => $matchId]);
        $match = $checkMatch->fetch();

        if (!$match) {
            throw new \RuntimeException('Match introuvable.');
        }

        if ($match['status'] !== 'scheduled') {
            throw new \RuntimeException('Impossible de pronostiquer un match deja termine.');
        }

        if ((int) $match['has_started'] === 1) {
            throw new \RuntimeException('Impossible de pronostiquer apres le debut du match.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO predictions (user_id, match_id, prediction_result)
             VALUES (:user_id, :match_id, :prediction_result)
             ON DUPLICATE KEY UPDATE
                prediction_result = VALUES(prediction_result),
                validated = 0,
                correct = NULL,
                points_earned = 0'
        );

        $stmt->execute([
            'user_id' => $userId,
            'match_id' => $matchId,
            'prediction_result' => $predictionResult,
        ]);
    }

    public function validateForMatch(int $matchId): void
    {
        $matchStmt = $this->pdo->prepare('SELECT result FROM matches WHERE match_id = :match_id LIMIT 1');
        $matchStmt->execute(['match_id' => $matchId]);
        $match = $matchStmt->fetch();

        if (!$match || empty($match['result'])) {
            throw new \RuntimeException('Resultat du match introuvable.');
        }

        $updateStmt = $this->pdo->prepare(
            'UPDATE predictions
             SET validated = 1,
                 correct = CASE WHEN prediction_result = :result THEN 1 ELSE 0 END,
                 points_earned = CASE WHEN prediction_result = :result THEN 1 ELSE 0 END
             WHERE match_id = :match_id'
        );

        $updateStmt->execute([
            'result' => $match['result'],
            'match_id' => $matchId,
        ]);

        $userManager = new UserManager();
        $userManager->recalculateAllPoints();
    }
}
