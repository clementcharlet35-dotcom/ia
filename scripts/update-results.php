<?php

declare(strict_types=1);

use App\Services\ResultSyncService;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$envPath = dirname(__DIR__);
if (file_exists($envPath . '/.env')) {
    Dotenv::createImmutable($envPath)->safeLoad();
}

$summary = (new ResultSyncService())->syncDueMatches();

echo json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
exit(($summary['success'] ?? false) ? 0 : 1);
