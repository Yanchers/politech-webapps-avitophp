# Спецификация проекта: Avito PHP

## 1. Обзор

Веб-приложение — упрощённый аналог Avito (доска объявлений).  
Реализация на чистом PHP (без фреймворков) с использованием MVC-архитектуры.  
СУБД: MySQL 5.7 через Docker (см. `docker-compose.yml`).

---

## 2. Роли и права доступа

| Роль | Псевдоним в БД | Описание |
|------|---------------|----------|
| Гость | — | Не авторизован. Только просмотр. |
| Клиент | `client` | Авторизован. Просмотр + создание объявлений, корзина, избранное, заказы. |
| Сотрудник | `employee` | Всё как у клиента + модерация объявлений. |
| Админ | `admin` | Полный доступ + CRUD-панель для всех таблиц. |

### Матрица прав

| Действие | Гость | Клиент | Сотрудник | Админ |
|---------|-------|--------|-----------|-------|
| Просмотр ленты объявлений | + | + | + | + |
| Поиск / фильтрация | + | + | + | + |
| Просмотр объявления | + | + | + | + |
| Регистрация / авторизация | + | — | — | — |
| Создание / редактирование своего объявления | — | + | + | + |
| Добавление в избранное | — | + | + | + |
| Корзина / оформление заказа | — | + | + | + |
| Просмотр своих заказов | — | + | + | + |
| Чат по объявлению | — | + | + | + |
| Модерация объявлений | — | — | + | + |
| CRUD-админка | — | — | — | + |
| Управление пользователями | — | — | — | + |

---

## 3. Страницы (роутинг)

| № | Маршрут | Страница | Доступ | Примечание |
|---|---------|----------|--------|------------|
| 1 | `/register` | Регистрация | Гость | Форма: email, phone, password, first_name, last_name, city_id |
| 2 | `/login` | Авторизация | Гость | Форма: email + password |
| 3 | `/logout` | Выход | Все | Удаление сессии, редирект на `/` |
| 4 | `/` | Главная (лента) | Все | Активные объявления + категории + строка поиска |
| 5 | `/search` | Поиск с фильтрами | Все | Фильтры слева: категория, город, цена, состояние. Сортировка. |
| 6 | `/ad/{id}` | Детальная объявления | Все | Фото, описание, цена, продавец, кнопки "в избранное"/"в корзину" |
| 7 | `/ad/create` | Создать объявление | client+ | Форма: категория, заголовок, описание, цена, состояние, город, фото |
| 8 | `/ad/{id}/edit` | Редактировать объявление | client+ (свой ad) | То же что create, но поля предзаполнены |
| 9 | `/profile` | Профиль пользователя | client+ | Данные пользователя, кнопка "мои объявления", "мои заказы" |
| 10 | `/profile/edit` | Редактирование профиля | client+ | Смена имени, телефона, города, пароля |
| 11 | `/profile/ads` | Мои объявления | client+ | Список объявлений текущего пользователя |
| 12 | `/favorites` | Избранное | client+ | Список избранных объявлений |
| 13 | `/cart` | Корзина | client+ | Товары в корзине, оформление заказа |
| 14 | `/order/create` | Оформление заказа | client+ | Подтверждение, редирект на успех |
| 15 | `/order/success` | Заказ оформлен | client+ | Номер заказа, детали, информация о письме |
| 16 | `/orders` | История заказов | client+ | Список заказов пользователя |
| 17 | `/order/{id}` | Детали заказа | client+ | Конкретный заказ |
| 18 | `/chat/{ad_id}/{user_id}` | Чат по объявлению | client+ | Обмен сообщениями между продавцом и покупателем |
| 19 | `/moderation` | Модерация | employee+ | Список объявлений со статусом moderation |
| 20 | `/moderation/{id}/approve` | Одобрить | employee+ | Меняет статус на active |
| 21 | `/moderation/{id}/reject` | Отклонить | employee+ | Меняет статус на rejected |
| 22 | `/admin` | Админ-панель (дашборд) | admin | Статистика, ссылки на CRUD |
| 23 | `/admin/{table}` | CRUD-список | admin | Список записей таблицы |
| 24 | `/admin/{table}/create` | CRUD-создание | admin | Форма создания |
| 25 | `/admin/{table}/{id}/edit` | CRUD-редактирование | admin | Форма редактирования |
| 26 | `/admin/{table}/{id}/delete` | CRUD-удаление | admin | Удаление записи |

> Для админки таблицы: roles, cities, categories, item_conditions, advertisement_statuses, users, advertisements, advertisement_images, orders, ad_chat_messages.  
> Таблицы user_sessions, user_basket, favorite_advertisements — системные, в CRUD не включаются (изменяются только через бизнес-логику).

---

## 4. Архитектура MVC (без фреймворков)

### 4.1. Структура директорий

```
project-root/
├── public/                  # Document root веб-сервера
│   ├── index.php            # Entry point (Front Controller)
│   ├── .htaccess            # rewrite rules
│   ├── css/
│   ├── js/
│   └── uploads/             # пользовательские изображения
├── src/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── AdController.php
│   │   ├── ProfileController.php
│   │   ├── SearchController.php
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   ├── FavoriteController.php
│   │   ├── ChatController.php
│   │   ├── ModerationController.php
│   │   └── AdminController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── City.php
│   │   ├── Category.php
│   │   ├── ItemCondition.php
│   │   ├── Advertisement.php
│   │   ├── AdvertisementImage.php
│   │   ├── AdvertisementStatus.php
│   │   ├── FavoriteAdvertisement.php
│   │   ├── Order.php
│   │   └── ChatMessage.php
│   ├── Repositories/
│   │   ├── UserRepository.php
│   │   ├── RoleRepository.php
│   │   ├── CityRepository.php
│   │   ├── CategoryRepository.php
│   │   ├── ItemConditionRepository.php
│   │   ├── AdvertisementRepository.php
│   │   ├── OrderRepository.php
│   │   └── ChatMessageRepository.php
│   ├── Views/
│   │   ├── layouts/
│   │   │   ├── header.php
│   │   │   ├── footer.php
│   │   │   └── admin_header.php
│   │   ├── auth/
│   │   ├── ad/
│   │   ├── profile/
│   │   ├── search/
│   │   ├── cart/
│   │   ├── order/
│   │   ├── favorites/
│   │   ├── chat/
│   │   ├── moderation/
│   │   ├── admin/
│   │   └── errors/
│   ├── Core/
│   │   ├── Router.php          # Разбор URL → Controller::action
│   │   ├── Controller.php      # Базовый контроллер
│   │   ├── Model.php           # Базовый класс модели (Active Record)
│   │   ├── Database.php        # Подключение к MySQL (PDO, Singleton)
│   │   ├── View.php            # Рендеринг шаблонов
│   │   ├── Request.php         # Обёртка над $_GET, $_POST, $_FILES
│   │   ├── Response.php        # Редиректы, JSON-ответы
│   │   ├── Session.php         # Управление сессией + авторизация
│   │   └── Validator.php       # Валидация форм
│   └── Middleware/
│       ├── AuthMiddleware.php  # Проверка авторизации
│       ├── GuestMiddleware.php # Только для гостей
│       ├── RoleMiddleware.php  # Проверка роли (client, employee, admin)
│       └── CsrfMiddleware.php  # CSRF-защита
├── config/
│   ├── app.php                 # Конфигурация приложения
│   └── database.php            # Параметры подключения к БД
├── db/
│   ├── db.sql                  # Схема БД (исходная)
│   └── seed.sql                # Начальные данные (роли, города, статусы, категории)
├── docker-compose.yml
├── .gitignore
├── .htaccess                   # Правила для корня (при необходимости)
└── project_spec.md             # Этот файл
```

### 4.2. Принципы работы MVC

1. **Front Controller** — `public/index.php`:
   - Подключает автозагрузчик классов (spl_autoload_register).
   - Загружает конфиги.
   - Запускает Router.

2. **Router** (`src/Core/Router.php`):
   - Разбирает `$_SERVER['REQUEST_URI']` и HTTP-метод.
   - Сопоставляет с таблицей маршрутов (Route → Controller@action).
   - Вызывает Middleware (если не пройдены → 403/редирект).
   - Создаёт экземпляр контроллера и вызывает нужный action.

3. **Controller** (`src/Core/Controller.php`):
   - Базовый класс: доступ к Request, Session, View.
   - Валидирует входные данные.
   - Вызывает Model для бизнес-логики.
   - Передаёт данные в View.

4. **Repository** (`src/Repositories`):
   - Каждый репозиторий использует только определенные, приближенные модели из `src/Models`.
   - Использует `Database.php` для PDO-запросов.

5. **View** (`src/Core/View.php`):
   - Простой PHP-шаблонизатор: `render($template, $data)`.
   - Шаблоны — чистый PHP с минимумом логики (только циклы и if).
   - Использует layouts (header/footer).

6. **Middleware**:
   - Выполняются ДО контроллера.
   - Прерывают выполнение, если проверка не пройдена.

### 4.3. Маршрутизация (пример структуры)

```php
// src/Core/Router.php — внутреннее представление
[
    'GET' => [
        '/'          => ['AdController', 'index'],
        '/login'     => ['AuthController', 'loginForm', ['guest']],
        '/register'  => ['AuthController', 'registerForm', ['guest']],
        '/ad/{id}'   => ['AdController', 'show'],
        '/admin'     => ['AdminController', 'dashboard', ['auth', 'role:admin']],
        // ...
    ],
    'POST' => [
        '/login'     => ['AuthController', 'login', ['guest']],
        '/register'  => ['AuthController', 'register', ['guest']],
        '/ad/create' => ['AdController', 'store', ['auth', 'role:client,employee,admin']],
        // ...
    ],
];
```

---

## 5. Замечания по БД

- Таблица `user_sessions` использует `expires_at DEFAULT CURRENT_TIMESTAMP` — в коде нужно при создании сессии явно задавать `expires_at = NOW() + INTERVAL 7 DAY`.
- `orders.ad_id` ссылается на `advertisements(ad_id)` без `ON DELETE CASCADE` — заказ хранит историю даже после удаления объявления.
- Товар после покупки не удаляется из корзин других пользователей — на фронтенде проверять статус `sold`.
- Для чата: `ad_chat_messages` хранит sender_id, receiver_id, ad_id — пользователь может начать чат с продавцом по конкретному объявлению.

---

## 6. Технические требования

- **PHP** 8.0+
- **MySQL** 5.7
- **Web-сервер**: Apache с mod_rewrite (или встроенный PHP-сервер для разработки)
- **Шаблонизация**: чистый PHP (встроенный)
- **Безопасность**: подготовленные запросы (PDO), хеширование паролей (password_hash), XSS-экранирование (htmlspecialchars)
- **Сессии**: хранятся в БД (таблица `user_sessions`), токен — в куках

---

## 7. Этапы разработки (план)

1. Настройка окружения (Docker, роутинг, автозагрузка)
2. Core (Database, Model, View, Controller, Router, Request, Response, Session)
3. Middleware (Auth, Guest, Role)
4. Модуль авторизации и регистрации
5. Модуль объявлений (CRUD, изображения)
6. Главная страница + поиск/фильтрация
7. Профиль пользователя
8. Избранное
9. Корзина и заказы
10. Модерация
11. Админ-панель
12. Чат
