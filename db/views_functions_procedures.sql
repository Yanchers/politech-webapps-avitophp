-- VIEWS
-- ad_view: полная денормализованная информация об объявлениях
CREATE OR REPLACE VIEW ad_view AS
SELECT a.*,
       c.name          AS category_name,
       ct.name         AS city_name,
       ic.name         AS condition_name,
       s.name          AS status_name,
       u.email         AS seller_email,
       u.first_name    AS seller_first_name,
       u.last_name     AS seller_last_name,
       ai.image_path   AS first_image_path
FROM advertisements a
JOIN categories c ON a.category_id = c.category_id
JOIN cities ct ON a.city_id = ct.city_id
JOIN item_conditions ic ON a.item_condition_id = ic.item_condition_id
JOIN advertisement_statuses s ON a.status_id = s.ad_status_id
LEFT JOIN users u ON a.seller_id = u.user_id
LEFT JOIN advertisement_images ai ON a.ad_id = ai.ad_id AND ai.sort_order = 1;

-- user_view: пользователи с названиями роли и города
CREATE OR REPLACE VIEW user_view AS
SELECT u.*, r.name AS role_name, c.name AS city_name
FROM users u
JOIN roles r ON u.role_id = r.role_id
JOIN cities c ON u.city_id = c.city_id;

-- order_item_view: позиции заказа с информацией о товаре и продавце
CREATE OR REPLACE VIEW order_item_view AS
SELECT oi.*,
       a.title                        AS ad_title,
       u.first_name                   AS seller_first_name,
       u.last_name                    AS seller_last_name,
       u.email                        AS seller_email,
       u.phone                        AS seller_phone,
       ai.image_path                  AS first_image_path
FROM order_items oi
JOIN advertisements a ON oi.ad_id = a.ad_id
JOIN users u ON a.seller_id = u.user_id
LEFT JOIN advertisement_images ai ON a.ad_id = ai.ad_id AND ai.sort_order = 1;

-- chat_message_view: сообщения с деталями отправителя, получателя и объявления
CREATE OR REPLACE VIEW chat_message_view AS
SELECT m.*,
       s.email       AS sender_email,
       s.first_name  AS sender_first_name,
       s.last_name   AS sender_last_name,
       r.email       AS receiver_email,
       r.first_name  AS receiver_first_name,
       r.last_name   AS receiver_last_name,
       a.title       AS ad_title
FROM ad_chat_messages m
JOIN users s ON m.sender_id = s.user_id
JOIN users r ON m.receiver_id = r.user_id
JOIN advertisements a ON m.ad_id = a.ad_id;

-- latest_chat_message_per_thread: последнее сообщение в каждом чате
CREATE OR REPLACE VIEW latest_chat_message_per_thread AS
SELECT MAX(ad_chat_message_id) AS max_id,
       ad_id,
       LEAST(sender_id, receiver_id)   AS user1,
       GREATEST(sender_id, receiver_id) AS user2
FROM ad_chat_messages
GROUP BY ad_id, LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id);

-- ~~~~~~~~~~~~~~~~~~~~~~~~~~ FUNCTIONS ~~~~~~~~~~~~~~~~~~~~~~~~~~~

DELIMITER $$

-- fn_ad_count: общее количество объявлений
DROP FUNCTION IF EXISTS fn_ad_count$$
CREATE FUNCTION fn_ad_count()
RETURNS INT DETERMINISTIC READS SQL DATA
BEGIN
    DECLARE cnt INT;
    SELECT COUNT(*) INTO cnt FROM advertisements;
    RETURN cnt;
END$$

-- fn_next_image_sort_order: следующий порядковый номер для изображения
DROP FUNCTION IF EXISTS fn_next_image_sort_order$$
CREATE FUNCTION fn_next_image_sort_order(p_ad_id INT)
RETURNS INT DETERMINISTIC READS SQL DATA
BEGIN
    DECLARE next INT;
    SELECT COALESCE(MAX(sort_order), 0) + 1 INTO next
    FROM advertisement_images
    WHERE ad_id = p_ad_id;
    RETURN next;
END$$

-- fn_is_in_basket: проверка наличия товара в корзине
DROP FUNCTION IF EXISTS fn_is_in_basket$$
CREATE FUNCTION fn_is_in_basket(p_user_id INT, p_ad_id INT)
RETURNS TINYINT DETERMINISTIC READS SQL DATA
BEGIN
    DECLARE exists_val TINYINT;
    SELECT COUNT(*) > 0 INTO exists_val
    FROM user_basket
    WHERE user_id = p_user_id AND ad_id = p_ad_id;
    RETURN exists_val;
END$$

-- fn_is_favorite: проверка избранного
DROP FUNCTION IF EXISTS fn_is_favorite$$
CREATE FUNCTION fn_is_favorite(p_user_id INT, p_ad_id INT)
RETURNS TINYINT DETERMINISTIC READS SQL DATA
BEGIN
    DECLARE exists_val TINYINT;
    SELECT COUNT(*) > 0 INTO exists_val
    FROM favorite_advertisements
    WHERE user_id = p_user_id AND ad_id = p_ad_id;
    RETURN exists_val;
END$$

-- fn_generate_order_number: генерация уникального номера заказа
DROP FUNCTION IF EXISTS fn_generate_order_number$$
CREATE FUNCTION fn_generate_order_number()
RETURNS VARCHAR(50) NOT DETERMINISTIC READS SQL DATA
BEGIN
    DECLARE num VARCHAR(50);
    DECLARE existing INT;
    REPEAT
        SET num = CONCAT('ORD-', UPPER(SUBSTRING(MD5(RAND()), 1, 7)));
        SELECT COUNT(*) INTO existing FROM orders WHERE order_number = num;
    UNTIL existing = 0 END REPEAT;
    RETURN num;
END$$

-- ~~~~~~~~~~~~~~~~~~~~~~~~~~ PROCEDURES ~~~~~~~~~~~~~~~~~~~~~~~~~~

-- sp_checkout: полное оформление заказа в одной транзакции.
-- Принимает ID покупателя, общую сумму и JSON-массив позиций.
-- Внутри одной транзакции: генерация номера → вставка заказа →
-- вставка позиций → пометка товаров как проданных → очистка корзины.
-- Возвращает order_id и order_number созданного заказа.
--
-- Пример вызова из PHP:
--   CALL sp_checkout(1, 1500.00, '[{"ad_id":1,"price_paid":500.00},{"ad_id":2,"price_paid":1000.00}]')
DROP PROCEDURE IF EXISTS sp_checkout$$
CREATE PROCEDURE sp_checkout(
    p_buyer_id      INT,
    p_total_amount  DECIMAL(10,2),
    p_items         JSON
)
BEGIN
    DECLARE v_order_id      INT;
    DECLARE v_order_number  VARCHAR(50);
    DECLARE v_idx           INT DEFAULT 0;
    DECLARE v_len           INT;
    DECLARE v_item          JSON;
    DECLARE v_ad_id         INT;
    DECLARE v_price         DECIMAL(10,2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- 1. Генерируем уникальный номер заказа
    SET v_order_number = fn_generate_order_number();

    -- 2. Создаём заголовок заказа
    INSERT INTO orders (order_number, buyer_id, total_amount)
    VALUES (v_order_number, p_buyer_id, p_total_amount);

    SET v_order_id = LAST_INSERT_ID();

    -- 3. Обрабатываем каждую позицию из JSON-массива
    SET v_len = JSON_LENGTH(p_items);

    WHILE v_idx < v_len DO
        SET v_item = JSON_EXTRACT(p_items, CONCAT('$[', v_idx, ']'));
        SET v_ad_id = JSON_UNQUOTE(JSON_EXTRACT(v_item, '$.ad_id'));
        SET v_price = JSON_UNQUOTE(JSON_EXTRACT(v_item, '$.price_paid'));

        INSERT INTO order_items (order_id, ad_id, price_paid)
        VALUES (v_order_id, v_ad_id, v_price);

        -- 4. Помечаем товар как проданный (status_id = 5 = 'sold')
        UPDATE advertisements SET status_id = 5 WHERE ad_id = v_ad_id;

        SET v_idx = v_idx + 1;
    END WHILE;

    -- 5. Очищаем корзину покупателя
    DELETE FROM user_basket WHERE user_id = p_buyer_id;

    COMMIT;

    -- Возвращаем ID и номер созданного заказа
    SELECT v_order_id AS order_id, v_order_number AS order_number;
END$$

DELIMITER ;
