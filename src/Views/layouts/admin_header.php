<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->escape($title ?? $app['name']) ?> — Админка</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header__inner">
                <a href="/admin" class="header__logo"><?= $this->escape($app['name']) ?> — Админка</a>

                <nav class="header__nav">
                    <a href="/admin" class="header__link">Дашборд</a>
                    <a href="/admin/cities" class="header__link">Города</a>
                    <a href="/admin/categories" class="header__link">Категории</a>
                    <a href="/admin/item_conditions" class="header__link">Состояния</a>
                    <a href="/admin/users" class="header__link">Пользователи</a>
                    <a href="/admin/advertisements" class="header__link">Объявления</a>
                    <a href="/admin/ad_chat_messages" class="header__link">Сообщения</a>
                    <a href="/" class="header__link">На сайт</a>
                    <a href="/logout" class="header__link">Выход</a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    var checkAll = document.querySelector('.admin-check-all');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            var items = document.querySelectorAll('.admin-check-item');
            items.forEach(function(item) { item.checked = checkAll.checked; });
        });
    }
});
</script>
