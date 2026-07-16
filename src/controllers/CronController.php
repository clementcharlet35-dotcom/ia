<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ResultSyncService;

final class CronController extends AbstractController
{
    public function updateResults(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        $expectedToken = (string) ($_ENV['RESULT_SYNC_TOKEN'] ?? '');

        if ($expectedToken === '' || !hash_equals($expectedToken, $token)) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Token invalide.']);
            return;
        }

        $summary = (new ResultSyncService())->syncDueMatches();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
