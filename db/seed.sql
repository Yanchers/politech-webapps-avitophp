-- Начальные данные для Avito PHP

-- Роли
INSERT IGNORE INTO roles (role_id, name) VALUES
(1, 'client'),
(2, 'employee'),
(3, 'admin');

-- Статусы объявлений
INSERT IGNORE INTO advertisement_statuses (ad_status_id, name) VALUES
(1, 'draft'),
(2, 'moderation'),
(3, 'active'),
(4, 'rejected'),
(5, 'sold'),
(6, 'deleted');

-- Состояния товара
INSERT IGNORE INTO item_conditions (item_condition_id, name) VALUES
(1, 'Новое'),
(2, 'Как новое'),
(3, 'Хорошее'),
(4, 'Среднее'),
(5, 'Плохое');

-- Города
INSERT IGNORE INTO cities (city_id, name) VALUES
(1, 'Москва'),
(2, 'Санкт-Петербург'),
(3, 'Новосибирск'),
(4, 'Екатеринбург'),
(5, 'Казань'),
(6, 'Краснодар');

-- Категории
INSERT IGNORE INTO categories (category_id, name, parent_id) VALUES
(1, 'Транспорт', NULL),
(2, 'Недвижимость', NULL),
(3, 'Электроника', NULL),
(4, 'Одежда', NULL),
(5, 'Для дома', NULL),
(6, 'Хобби и спорт', NULL),
(7, 'Автомобили', 1),
(8, 'Велосипеды', 1),
(9, 'Квартиры', 2),
(10, 'Дома и дачи', 2),
(11, 'Смартфоны', 3),
(12, 'Ноутбуки', 3),
(13, 'Мебель', 5),
(14, 'Бытовая техника', 5);

-- Админ (пароль: admin123)
INSERT IGNORE INTO users (user_id, email, phone, password_hash, first_name, last_name, role_id, city_id) VALUES
(1, 'admin@avito.local', '+70000000001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Админ', 'Админов', 3, 1);
