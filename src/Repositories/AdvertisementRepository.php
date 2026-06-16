<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Advertisement;
use App\Models\AdvertisementImage;

class AdvertisementRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?Advertisement
    {
        $sql = "SELECT a.*, c.name AS category_name, ct.name AS city_name,
                       ic.name AS condition_name, s.name AS status_name
                FROM advertisements a
                JOIN categories c ON a.category_id = c.category_id
                JOIN cities ct ON a.city_id = ct.city_id
                JOIN item_conditions ic ON a.item_condition_id = ic.item_condition_id
                JOIN advertisement_statuses s ON a.status_id = s.ad_status_id
                WHERE a.ad_id = ?";
        $row = $this->db->fetch($sql, [$id]);
        return $row ? $this->hydrate($row) : null;
    }

    public function findAllActive(
        ?int $categoryId = null,
        ?int $cityId = null,
        ?string $search = null,
        ?int $itemConditionId = null,
        ?float $priceMin = null,
        ?float $priceMax = null,
        string $sort = 'date_desc',
        int $limit = 20,
        int $offset = 0
    ): array
    {
        $sql = "SELECT a.*, c.name AS category_name, ct.name AS city_name,
                       ic.name AS condition_name, s.name AS status_name,
                       ai.image_path as first_image_path
                FROM advertisements a
                LEFT JOIN advertisement_images ai ON a.ad_id = ai.ad_id AND ai.sort_order = 1
                JOIN categories c ON a.category_id = c.category_id
                JOIN cities ct ON a.city_id = ct.city_id
                JOIN item_conditions ic ON a.item_condition_id = ic.item_condition_id
                JOIN advertisement_statuses s ON a.status_id = s.ad_status_id
                WHERE a.status_id = 3";

        $params = [];

        if ($categoryId) {
            $sql .= " AND (a.category_id = ? OR c.parent_id = ?)";
            $params[] = $categoryId;
            $params[] = $categoryId;
        }

        if ($cityId) {
            $sql .= " AND a.city_id = ?";
            $params[] = $cityId;
        }

        if ($itemConditionId) {
            $sql .= " AND a.item_condition_id = ?";
            $params[] = $itemConditionId;
        }

        if ($priceMin !== null) {
            $sql .= " AND a.price >= ?";
            $params[] = $priceMin;
        }

        if ($priceMax !== null) {
            $sql .= " AND a.price <= ?";
            $params[] = $priceMax;
        }

        if ($search) {
            $sql .= " AND (a.title LIKE ? OR a.description LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sort = match ($sort) {
            'date_asc' => 'a.created_at ASC',
            'price_asc' => 'a.price ASC',
            'price_desc' => 'a.price DESC',
            default => 'a.created_at DESC',
        };
        $sql .= " ORDER BY {$sort} LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll($sql, $params));
    }

    public function findBySellerId(int $sellerId): array
    {
        $sql = "SELECT a.*, c.name AS category_name, ct.name AS city_name,
                       ic.name AS condition_name, s.name AS status_name
                FROM advertisements a
                JOIN categories c ON a.category_id = c.category_id
                JOIN cities ct ON a.city_id = ct.city_id
                JOIN item_conditions ic ON a.item_condition_id = ic.item_condition_id
                JOIN advertisement_statuses s ON a.status_id = s.ad_status_id
                WHERE a.seller_id = ?
                ORDER BY a.created_at DESC";
        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll($sql, [$sellerId]));
    }

    public function countActive(
        ?int $categoryId = null,
        ?int $cityId = null,
        ?string $search = null,
        ?int $itemConditionId = null,
        ?float $priceMin = null,
        ?float $priceMax = null
    ): int
    {
        $sql = "SELECT COUNT(*) AS cnt FROM advertisements a WHERE a.status_id = 3";
        $params = [];

        if ($categoryId) {
            $sql .= " AND (a.category_id = ? OR a.category_id IN (SELECT category_id FROM categories WHERE parent_id = ?))";
            $params[] = $categoryId;
            $params[] = $categoryId;
        }

        if ($cityId) {
            $sql .= " AND a.city_id = ?";
            $params[] = $cityId;
        }

        if ($itemConditionId) {
            $sql .= " AND a.item_condition_id = ?";
            $params[] = $itemConditionId;
        }

        if ($priceMin !== null) {
            $sql .= " AND a.price >= ?";
            $params[] = $priceMin;
        }

        if ($priceMax !== null) {
            $sql .= " AND a.price <= ?";
            $params[] = $priceMax;
        }

        if ($search) {
            $sql .= " AND (a.title LIKE ? OR a.description LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $result = $this->db->fetch($sql, $params);
        return (int) ($result['cnt'] ?? 0);
    }

    public function create(array $data): Advertisement
    {
        if (!isset($data['status_id'])) {
            $data['status_id'] = 2;
        }
        $id = $this->db->insert('advertisements', $data);
        return $this->findById($id);
    }

    public function update(int $id, array $data): void
    {
        $this->db->update('advertisements', $data, 'ad_id = ?', [$id]);
    }

    public function delete(int $id): void
    {
        $this->db->delete('advertisements', 'ad_id = ?', [$id]);
    }

    public function getImages(int $adId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM advertisement_images WHERE ad_id = ? ORDER BY sort_order ASC",
            [$adId]
        );
        return array_map(fn($row) => $this->hydrateImage($row), $rows);
    }

    public function addImage(int $adId, string $path, int $sortOrder): void
    {
        $this->db->insert('advertisement_images', [
            'ad_id' => $adId,
            'image_path' => $path,
            'sort_order' => $sortOrder,
        ]);
    }

    public function deleteImage(int $imageId): void
    {
        $row = $this->db->fetch("SELECT * FROM advertisement_images WHERE image_id = ?", [$imageId]);
        if ($row) {
            $filePath = __DIR__ . '/../../public/' . $row['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->db->delete('advertisement_images', 'image_id = ?', [$imageId]);
        }
    }

    public function getNextSortOrder(int $adId): int
    {
        $result = $this->db->fetch(
            "SELECT COALESCE(MAX(sort_order), 0) + 1 AS next FROM advertisement_images WHERE ad_id = ?",
            [$adId]
        );
        return (int) ($result['next'] ?? 1);
    }

    private function hydrate(array $row): Advertisement
    {
        $ad = new Advertisement();
        foreach ($row as $key => $value) {
            $ad->$key = $value;
        }
        return $ad;
    }

    private function hydrateImage(array $row): AdvertisementImage
    {
        $image = new AdvertisementImage();
        foreach ($row as $key => $value) {
            $image->$key = $value;
        }
        return $image;
    }
}
