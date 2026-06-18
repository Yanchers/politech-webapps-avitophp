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

function console_log($data) {
    // Safely encode arrays or objects to JSON strings
    $js_code = 'console.log(' . json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ');';
    
    // Wrap inside standard script tags and output to browser
    echo '<script>' . $js_code . '</script>';
}

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

// --- Профиль ---
$router->get('/profile', 'ProfileController@index', ['auth']);
$router->get('/profile/edit', 'ProfileController@edit', ['auth']);
$router->post('/profile/edit', 'ProfileController@update', ['auth']);

// --- Избранное ---
$router->get('/favorites', 'FavoriteController@index', ['auth']);
$router->post('/favorites/add/{adId}', 'FavoriteController@add', ['auth']);
$router->post('/favorites/remove/{adId}', 'FavoriteController@remove', ['auth']);

// --- Корзина ---
$router->get('/cart', 'CartController@index', ['auth']);
$router->post('/cart/add/{adId}', 'CartController@add', ['auth']);
$router->post('/cart/remove/{adId}', 'CartController@remove', ['auth']);

// --- Заказы ---
$router->get('/order/create', 'OrderController@create', ['auth']);
$router->post('/order/create', 'OrderController@store', ['auth']);
$router->get('/order/success', 'OrderController@success', ['auth']);
$router->get('/orders', 'OrderController@index', ['auth']);
$router->get('/order/{orderNumber}', 'OrderController@show', ['auth']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);