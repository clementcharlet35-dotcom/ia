<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;
use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    Dotenv::createImmutable(__DIR__)->safeLoad();
}

try {
    $pdo = Database::getConnection();
    echo '<h1>Connexion réussie</h1>';
    echo '<p>Base connectée avec succès.</p>';
} catch (Throwable $e) {
    echo '<h1>Erreur de connexion</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}
