<?php

namespace App\Repositories;

use App\Core\Database;

class CartRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getCartItems(int $userId): array
    {
        $sql = "SELECT a.*, c.name AS category_name, ct.name AS city_name,
                       ic.name AS condition_name, s.name AS status_name,
                       ai.image_path AS first_image_path,
                       u.first_name AS seller_first_name, u.last_name AS seller_last_name
                FROM user_basket ub
                JOIN ad_view a ON ub.ad_id = a.ad_id
                LEFT JOIN advertisement_images ai ON a.ad_id = ai.ad_id AND ai.sort_order = 1
                JOIN categories c ON a.category_id = c.category_id
                JOIN cities ct ON a.city_id = ct.city_id
                JOIN item_conditions ic ON a.item_condition_id = ic.item_condition_id
                JOIN advertisement_statuses s ON a.status_id = s.ad_status_id
                JOIN users u ON a.seller_id = u.user_id
                WHERE ub.user_id = ?
                ORDER BY a.created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getCartItemsWithSeller(int $userId): array
    {
        $sql = "SELECT a.*, u.first_name AS seller_first_name, u.last_name AS seller_last_name,
                       ai.image_path AS first_image_path
                FROM user_basket ub
                JOIN ad_view a ON ub.ad_id = a.ad_id
                LEFT JOIN advertisement_images ai ON a.ad_id = ai.ad_id AND ai.sort_order = 1
                JOIN users u ON a.seller_id = u.user_id
                WHERE ub.user_id = ? AND a.status_id = 3";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getActiveCartItems(int $userId): array
    {
        $sql = "SELECT a.* FROM user_basket ub
                JOIN ad_view a ON ub.ad_id = a.ad_id
                WHERE ub.user_id = ? AND a.status_id = 3";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function inBasket(int $userId, int $adId): bool
    {
        $result = $this->db->fetch(
            "SELECT fn_is_in_basket(?, ?) AS res",
            [$userId, $adId]
        );
        return (bool) ($result['res'] ?? false);
    }

    public function add(int $userId, int $adId): void
    {
        $this->db->insert('user_basket', [
            'user_id' => $userId,
            'ad_id' => $adId,
        ]);
    }

    public function remove(int $userId, int $adId): void
    {
        $this->db->delete('user_basket', 'user_id = ? AND ad_id = ?', [$userId, $adId]);
    }

    public function clear(int $userId): void
    {
        $this->db->delete('user_basket', 'user_id = ?', [$userId]);
    }
}
