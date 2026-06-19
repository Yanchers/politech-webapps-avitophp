-- роли пользователей 'client', 'employee', 'admin'
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- города
CREATE TABLE IF NOT EXISTS cities (
    city_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- категории и подкатегории
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT NULL,

    FOREIGN KEY (parent_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

-- состояние товара
CREATE TABLE IF NOT EXISTS item_conditions (
    item_condition_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- статусы объявления (ENUM('draft', 'moderation', 'active', 'rejected', 'sold', 'deleted') DEFAULT 'moderation')
CREATE TABLE IF NOT EXISTS advertisement_statuses (
	ad_status_id INT AUTO_INCREMENT PRIMARY KEY,
	name varchar(100) NOT NULL 
);

-- пользователи (1 пользователь - 1 роль - 1 город)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role_id INT NOT NULL,
    city_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id) REFERENCES roles(role_id),
    FOREIGN KEY (city_id) REFERENCES cities(city_id)
);

CREATE TABLE IF NOT EXISTS user_sessions (
    session_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash varchar(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE -- нам не нужны сессии если пользователя удалят
);


-- объявления
CREATE TABLE IF NOT EXISTS advertisements (
    ad_id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    item_condition_id INT NOT NULL,
    city_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    status_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (seller_id) REFERENCES users(user_id) ON DELETE CASCADE, -- если удален юзер, удаляются его объявления
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (item_condition_id) REFERENCES item_conditions(item_condition_id),
    FOREIGN KEY (status_id) REFERENCES advertisement_statuses(ad_status_id),
    FOREIGN KEY (city_id) REFERENCES cities(city_id)
);
-- фотографии объявлений (1 объявление - много фото)
CREATE TABLE IF NOT EXISTS advertisement_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL,

    FOREIGN KEY (ad_id) REFERENCES advertisements(ad_id) ON DELETE CASCADE
);

-- избранные объявления пользователя (много пользователей - много объявлений)
CREATE TABLE IF NOT EXISTS favorite_advertisements(
    user_id INT NOT NULL,
    ad_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id, ad_id),
    FOREIGN KEY (ad_id) REFERENCES advertisements(ad_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- корзина (надо не забыть, что если товар купили, то нужно удалить его из корзин других пользователей, либо просто помечать на фронтенде, что товар выкупили)
CREATE TABLE IF NOT EXISTS user_basket(
    user_id INT NOT NULL,
    ad_id INT NOT NULL,
    -- уникальный индекс, чтобы пользователь не мог добавить один и тот же товар дважды
    PRIMARY KEY (user_id, ad_id),
    FOREIGN KEY (ad_id) REFERENCES advertisements(ad_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
-- заказы (заголовок заказа)
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    buyer_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (buyer_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- позиции заказа (купленные товары)
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    ad_id INT NOT NULL,
    price_paid DECIMAL(10, 2) NOT NULL,
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES advertisements(ad_id)
);

CREATE TABLE IF NOT EXISTS ad_chat_messages(
    ad_chat_message_id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES advertisements(ad_id) ON DELETE CASCADE
);