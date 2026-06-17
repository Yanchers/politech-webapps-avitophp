<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;

class FavoriteController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $userId = $this->session->getUserId();

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

        $favorites = $this->db->fetchAll($sql, [$userId]);

        $this->render('favorites/index', [
            'title' => 'Избранное',
            'favorites' => $favorites,
        ]);
    }

    public function add(int $adId): void
    {
        $userId = $this->session->getUserId();

        $existing = $this->db->fetch(
            "SELECT * FROM favorite_advertisements WHERE user_id = ? AND ad_id = ?",
            [$userId, $adId]
        );

        if (!$existing) {
            $this->db->insert('favorite_advertisements', [
                'user_id' => $userId,
                'ad_id' => $adId,
            ]);
            $this->session->setFlash('success', 'Объявление добавлено в избранное');
        }

        $this->back();
    }

    public function remove(int $adId): void
    {
        $userId = $this->session->getUserId();

        $this->db->delete('favorite_advertisements', 'user_id = ? AND ad_id = ?', [$userId, $adId]);
        $this->session->setFlash('success', 'Объявление удалено из избранного');

        $this->back();
    }
}
