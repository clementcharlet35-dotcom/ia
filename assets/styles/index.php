<?php

declare(strict_types=1);

use App\Config\Router;
use Dotenv\Dotenv;

session_start();

require_once __DIR__ . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Chargement du fichier .env
|--------------------------------------------------------------------------
*/
$envPath = __DIR__;

if (file_exists($envPath . '/.env')) {
    $dotenv = Dotenv::createImmutable($envPath);
    $dotenv->safeLoad();
}

/*
|--------------------------------------------------------------------------
| Initialisation du routeur
|--------------------------------------------------------------------------
*/
$router = new Router();

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/
$router->get('/', 'HomeController@index');
$router->get('/legal', 'HomeController@legal');

$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@loginPost');

$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@registerPost');

$router->get('/logout', 'AuthController@logout');

$router->get('/search', 'SearchController@index');

/*
|--------------------------------------------------------------------------
| Routes utilisateur
|--------------------------------------------------------------------------
*/
$router->get('/predictions', 'PredictionController@index');
$router->post('/predictions', 'PredictionController@store');

$router->get('/leaderboard', 'LeaderboardController@index');
$router->post('/leaderboard/users/delete', 'LeaderboardController@deleteUser');

/*
|--------------------------------------------------------------------------
| Routes admin
|--------------------------------------------------------------------------
*/
$router->get('/admin', 'AdminController@index');

$router->post('/admin/matches/store', 'AdminController@storeMatch');
$router->post('/admin/matches/delete', 'AdminController@deleteMatch');

$router->post('/admin/results/store', 'AdminController@storeResult');
$router->post('/admin/results/sync', 'AdminController@syncResults');

/*
|--------------------------------------------------------------------------
| Routes système / Cron
|--------------------------------------------------------------------------
*/
$router->get('/cron/update-results', 'CronController@updateResults');

/*
|--------------------------------------------------------------------------
| Exécution de la route demandée
|--------------------------------------------------------------------------
*/
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);