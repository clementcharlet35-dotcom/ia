<?php

declare(strict_types=1);

namespace App\Services;

use App\Managers\MatchManager;
use App\Managers\PredictionManager;
use App\Managers\ResultSyncSchemaManager;

final class ResultSyncService
{
    public function syncDueMatches(bool $forcePastMatches = false): array
    {
        (new ResultSyncSchemaManager())->ensureColumns();

        $client = new FootballDataClient();
        if (!$client->isConfigured()) {
            return [
                'success' => false,
                'message' => 'FOOTBALL_DATA_TOKEN est vide dans le fichier .env.',
                'checked' => 0,
                'updated' => 0,
                'errors' => [],
            ];
        }

        $limit = max(1, (int) ($_ENV['RESULT_SYNC_MAX_CALLS_PER_RUN'] ?? 5));
        $delaySeconds = max(0, (int) ($_ENV['RESULT_SYNC_DELAY_SECONDS'] ?? 7));
        $matchManager = new MatchManager();
        $predictionManager = new PredictionManager();
        $matches = $forcePastMatches
            ? $matchManager->findPastScheduledMatchesForResultSync($limit)
            : $matchManager->findMatchesDueForResultSync($limit);
        $summary = [
            'success' => true,
            'message' => 'Synchronisation terminee.',
            'checked' => 0,
            'updated' => 0,
            'errors' => [],
        ];

        foreach ($matches as $index => $match) {
            $checkpoint = (int) $match['result_sync_checkpoint'];
            $summary['checked']++;

            try {
                $score = $client->findFinishedScoreForMatch($match);
                if ($checkpoint > 0) {
                    $matchManager->markResultCheckAttempt((int) $match['match_id'], $checkpoint);
                }

                if ($score === null) {
                    $matchManager->setResultSyncError((int) $match['match_id'], null);
                    continue;
                }

                $matchManager->setResult(
                    (int) $match['match_id'],
                    (int) $score['result_team1'],
                    (int) $score['result_team2'],
                    $score['api_match_id'] !== '' ? (string) $score['api_match_id'] : null
                );
                $predictionManager->validateForMatch((int) $match['match_id']);
                $summary['updated']++;
            } catch (\Throwable $exception) {
                $message = substr($exception->getMessage(), 0, 255);
                $matchManager->setResultSyncError((int) $match['match_id'], $message);
                $summary['errors'][] = [
                    'match_id' => (int) $match['match_id'],
                    'message' => $message,
                ];
            }

            if ($delaySeconds > 0 && $index < count($matches) - 1) {
                sleep($delaySeconds);
            }
        }

        return $summary;
    }
}
