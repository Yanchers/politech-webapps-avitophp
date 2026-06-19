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

// --- Модерация ---
$router->get('/moderation', 'ModerationController@index', ['auth', 'role:employee,admin']);
$router->post('/moderation/{id}/approve', 'ModerationController@approve', ['auth', 'role:employee,admin']);
$router->post('/moderation/{id}/reject', 'ModerationController@reject', ['auth', 'role:employee,admin']);

// --- Админ-панель ---
$router->get('/admin', 'AdminController@dashboard', ['auth', 'role:admin']);
$router->get('/admin/cities', 'AdminController@citiesIndex', ['auth', 'role:admin']);
$router->get('/admin/cities/create', 'AdminController@citiesCreate', ['auth', 'role:admin']);
$router->post('/admin/cities/store', 'AdminController@citiesStore', ['auth', 'role:admin']);
$router->get('/admin/cities/{id}/edit', 'AdminController@citiesEdit', ['auth', 'role:admin']);
$router->post('/admin/cities/{id}/update', 'AdminController@citiesUpdate', ['auth', 'role:admin']);
$router->get('/admin/cities/{id}/delete', 'AdminController@citiesDelete', ['auth', 'role:admin']);
$router->post('/admin/cities/batch-delete', 'AdminController@citiesBatchDelete', ['auth', 'role:admin']);

$router->get('/admin/categories', 'AdminController@categoriesIndex', ['auth', 'role:admin']);
$router->get('/admin/categories/create', 'AdminController@categoriesCreate', ['auth', 'role:admin']);
$router->post('/admin/categories/store', 'AdminController@categoriesStore', ['auth', 'role:admin']);
$router->get('/admin/categories/{id}/edit', 'AdminController@categoriesEdit', ['auth', 'role:admin']);
$router->post('/admin/categories/{id}/update', 'AdminController@categoriesUpdate', ['auth', 'role:admin']);
$router->get('/admin/categories/{id}/delete', 'AdminController@categoriesDelete', ['auth', 'role:admin']);
$router->post('/admin/categories/batch-delete', 'AdminController@categoriesBatchDelete', ['auth', 'role:admin']);

$router->get('/admin/item_conditions', 'AdminController@itemConditionsIndex', ['auth', 'role:admin']);
$router->get('/admin/item_conditions/create', 'AdminController@itemConditionsCreate', ['auth', 'role:admin']);
$router->post('/admin/item_conditions/store', 'AdminController@itemConditionsStore', ['auth', 'role:admin']);
$router->get('/admin/item_conditions/{id}/edit', 'AdminController@itemConditionsEdit', ['auth', 'role:admin']);
$router->post('/admin/item_conditions/{id}/update', 'AdminController@itemConditionsUpdate', ['auth', 'role:admin']);
$router->get('/admin/item_conditions/{id}/delete', 'AdminController@itemConditionsDelete', ['auth', 'role:admin']);
$router->post('/admin/item_conditions/batch-delete', 'AdminController@itemConditionsBatchDelete', ['auth', 'role:admin']);

$router->get('/admin/users', 'AdminController@usersIndex', ['auth', 'role:admin']);
$router->get('/admin/users/create', 'AdminController@usersCreate', ['auth', 'role:admin']);
$router->post('/admin/users/store', 'AdminController@usersStore', ['auth', 'role:admin']);
$router->get('/admin/users/{id}/edit', 'AdminController@usersEdit', ['auth', 'role:admin']);
$router->post('/admin/users/{id}/update', 'AdminController@usersUpdate', ['auth', 'role:admin']);
$router->get('/admin/users/{id}/delete', 'AdminController@usersDelete', ['auth', 'role:admin']);
$router->post('/admin/users/batch-delete', 'AdminController@usersBatchDelete', ['auth', 'role:admin']);

$router->get('/admin/advertisements', 'AdminController@advertisementsIndex', ['auth', 'role:admin']);
$router->get('/admin/advertisements/create', 'AdminController@advertisementsCreate', ['auth', 'role:admin']);
$router->post('/admin/advertisements/store', 'AdminController@advertisementsStore', ['auth', 'role:admin']);
$router->get('/admin/advertisements/{id}/edit', 'AdminController@advertisementsEdit', ['auth', 'role:admin']);
$router->post('/admin/advertisements/{id}/update', 'AdminController@advertisementsUpdate', ['auth', 'role:admin']);
$router->get('/admin/advertisements/{id}/delete', 'AdminController@advertisementsDelete', ['auth', 'role:admin']);
$router->post('/admin/advertisements/batch-delete', 'AdminController@advertisementsBatchDelete', ['auth', 'role:admin']);

$router->get('/admin/ad_chat_messages', 'AdminController@adChatMessagesIndex', ['auth', 'role:admin']);
$router->get('/admin/ad_chat_messages/create', 'AdminController@adChatMessagesCreate', ['auth', 'role:admin']);
$router->post('/admin/ad_chat_messages/store', 'AdminController@adChatMessagesStore', ['auth', 'role:admin']);
$router->get('/admin/ad_chat_messages/{id}/edit', 'AdminController@adChatMessagesEdit', ['auth', 'role:admin']);
$router->post('/admin/ad_chat_messages/{id}/update', 'AdminController@adChatMessagesUpdate', ['auth', 'role:admin']);
$router->get('/admin/ad_chat_messages/{id}/delete', 'AdminController@adChatMessagesDelete', ['auth', 'role:admin']);
$router->post('/admin/ad_chat_messages/batch-delete', 'AdminController@adChatMessagesBatchDelete', ['auth', 'role:admin']);

// --- Чат ---
$router->get('/chat', 'ChatController@index', ['auth']);
$router->get('/chat/{adId}/{userId}', 'ChatController@show', ['auth']);
$router->post('/chat/{adId}/{userId}', 'ChatController@store', ['auth']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);