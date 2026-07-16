<?php

declare(strict_types=1);

use App\Config\Router;
use Dotenv\Dotenv;

$sessionCookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => $sessionCookieParams['lifetime'],
    'path' => $sessionCookieParams['path'],
    'domain' => $sessionCookieParams['domain'],
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/vendor/autoload.php';

$envPath = __DIR__;
if (file_exists($envPath . '/.env')) {
    $dotenv = Dotenv::createImmutable($envPath);
    $dotenv->safeLoad();
}

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/legal', 'HomeController@legal');
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@loginPost');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@registerPost');
$router->post('/logout', 'AuthController@logout');

$router->get('/predictions', 'PredictionController@index');
$router->post('/predictions', 'PredictionController@store');

$router->get('/leaderboard', 'LeaderboardController@index');
$router->post('/leaderboard/users/delete', 'LeaderboardController@deleteUser');
$router->get('/search', 'SearchController@index');

$router->get('/admin', 'AdminController@index');
$router->post('/admin/matches/store', 'AdminController@storeMatch');
$router->post('/admin/matches/delete', 'AdminController@deleteMatch');
$router->post('/admin/results/sync', 'AdminController@syncResults');

$router->get('/cron/update-results', 'CronController@updateResults');

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
