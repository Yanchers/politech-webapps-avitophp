<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->escape($title ?? $app['name']) ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header__inner">
                <a href="/" class="header__logo"><?= $this->escape($app['name']) ?></a>

                <div class="header__search">
                    <form action="/search" method="GET" class="header__search-form">
                        <input type="text" name="q" class="header__search-input" placeholder="Поиск по объявлениям..." value="">
                        <button type="submit" class="header__search-btn">Найти</button>
                    </form>
                </div>

                <nav class="header__nav">
                    <a href="/" class="header__link">Главная</a>
                    <?php if (isset($user) && $user): ?>
                        <a href="/favorites" class="header__link">Избранное</a>
                        <a href="/cart" class="header__link">Корзина</a>
                        <a href="/chat" class="header__link">Сообщения</a>
                        <a href="/profile" class="header__link">Профиль</a>
                        <?php if ($user['role_name'] === 'employee' || $user['role_name'] === 'admin'): ?>
                            <a href="/moderation" class="header__link">Модерация</a>
                        <?php endif; ?>
                        <?php if ($user['role_name'] === 'admin'): ?>
                            <a href="/admin" class="header__link">Админка</a>
                        <?php endif; ?>
                        <a href="/ad/create" class="header__link header__link--primary">+ Объявление</a>
                        <a href="/logout" class="header__link">Выход</a>
                    <?php else: ?>
                        <a href="/login" class="header__link">Войти</a>
                        <a href="/register" class="header__link header__link--primary">Регистрация</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">

<?php
$flash = \App\Core\Session::getInstance();
$error = $flash->getFlash('error');
$success = $flash->getFlash('success');
if ($error): ?>
    <div class="alert alert--error"><?= $this->escape($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert--success"><?= $this->escape($success) ?></div>
<?php endif; ?>
