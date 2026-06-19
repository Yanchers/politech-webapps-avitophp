<?php

namespace App\Repositories;

use App\Core\Database;

class FavoriteAdvertisementRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByUserId(int $userId): array
    {
        $sql = "SELECT a.*, c.name AS category_name, ct.name AS city_name,
                       ic.name AS condition_name, s.name AS status_name,
                       ai.image_path AS first_image_path,
                       fa.added_at
                FROM favorite_advertisements fa
                JOIN advertisements a ON fa.ad_id = a.ad_id
                LEFT JOIN advertisement_images ai ON a.ad_id = ai.ad_id AND ai.sort_order = 1
                JOIN categories c ON a.category_id = c.category_id
                JOIN cities ct ON a.city_id = ct.city_id
                JOIN item_conditions ic ON a.item_condition_id = ic.item_condition_id
                JOIN advertisement_statuses s ON a.status_id = s.ad_status_id
                WHERE fa.user_id = ?
                ORDER BY fa.added_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function exists(int $userId, int $adId): bool
    {
        $row = $this->db->fetch(
            "SELECT 1 FROM favorite_advertisements WHERE user_id = ? AND ad_id = ?",
            [$userId, $adId]
        );
        return $row !== null;
    }

    public function add(int $userId, int $adId): void
    {
        $this->db->insert('favorite_advertisements', [
            'user_id' => $userId,
            'ad_id' => $adId,
        ]);
    }

    public function remove(int $userId, int $adId): void
    {
        $this->db->delete('favorite_advertisements', 'user_id = ? AND ad_id = ?', [$userId, $adId]);
    }
}
