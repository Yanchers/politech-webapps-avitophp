<?php

/**
 * Front Controller — точка входа
 */

require_once __DIR__ . '/../autoload.php';

use App\Core\Database;
use App\Core\Router;
use App\Core\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';

Database::init($dbConfig);

Session::getInstance();

$router = new Router();

$router->addMiddlewareAlias('auth', fn() => new AuthMiddleware());
$router->addMiddlewareAlias('guest', fn() => new GuestMiddleware());

// --- Аутентификация ---
$router->get('/login', 'AuthController@loginForm', ['guest']);
$router->post('/login', 'AuthController@login', ['guest']);
$router->get('/register', 'AuthController@registerForm', ['guest']);
$router->post('/register', 'AuthController@register', ['guest']);
$router->get('/logout', 'AuthController@logout', ['auth']);

// --- Главная ---
$router->get('/', 'HomeController@index');

// --- Поиск ---
$router->get('/search', 'SearchController@index');

// --- Объявления ---
$router->get('/ad/create', 'AdController@create', ['auth']);
$router->post('/ad/create', 'AdController@store', ['auth']);
$router->get('/ad/{id}', 'AdController@show');
$router->get('/ad/{id}/edit', 'AdController@edit', ['auth']);
$router->post('/ad/{id}/edit', 'AdController@update', ['auth']);
$router->post('/ad/{id}/delete', 'AdController@destroy', ['auth']);
$router->get('/profile/ads', 'AdController@userAds', ['auth']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
