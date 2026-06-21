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
        $row = $this->db->fetch("SELECT * FROM ad_view WHERE ad_id = ?", [$id]);
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
        $sql = "SELECT * FROM ad_view WHERE status_id = 3";
        $params = [];

        if ($categoryId) {
            $sql .= " AND (category_id = ? OR category_id IN (SELECT category_id FROM categories WHERE parent_id = ?))";
            $params[] = $categoryId;
            $params[] = $categoryId;
        }

        if ($cityId) {
            $sql .= " AND city_id = ?";
            $params[] = $cityId;
        }

        if ($itemConditionId) {
            $sql .= " AND item_condition_id = ?";
            $params[] = $itemConditionId;
        }

        if ($priceMin !== null && $priceMin != 0) {
            $sql .= " AND price >= ?";
            $params[] = $priceMin;
        }

        if ($priceMax !== null && $priceMax != 0) {
            $sql .= " AND price <= ?";
            $params[] = $priceMax;
        }

        if ($search) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sort = match ($sort) {
            'date_asc' => 'created_at ASC',
            'price_asc' => 'price ASC',
            'price_desc' => 'price DESC',
            default => 'created_at DESC',
        };
        $sql .= " ORDER BY {$sort} LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll($sql, $params));
    }

    public function findByStatus(int $statusId): array
    {
        $sql = "SELECT * FROM ad_view WHERE status_id = ? ORDER BY created_at DESC";
        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll($sql, [$statusId]));
    }

    public function findBySellerId(int $sellerId): array
    {
        $sql = "SELECT * FROM ad_view WHERE seller_id = ? ORDER BY created_at DESC";
        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll($sql, [$sellerId]));
    }

    public function findAllWithSellers(): array
    {
        return array_map(
            fn($row) => $this->hydrate($row),
            $this->db->fetchAll("SELECT * FROM ad_view ORDER BY created_at DESC")
        );
    }

    public function findAllSimple(): array
    {
        return $this->db->fetchAll("SELECT ad_id, title FROM advertisements ORDER BY ad_id DESC");
    }

    public function getAllStatuses(): array
    {
        return $this->db->fetchAll("SELECT * FROM advertisement_statuses ORDER BY ad_status_id ASC");
    }

    public function count(): int
    {
        $result = $this->db->fetch("SELECT fn_ad_count() AS cnt");
        return (int) ($result['cnt'] ?? 0);
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
        $sql = "SELECT COUNT(*) AS cnt FROM ad_view WHERE status_id = 3";
        $params = [];

        if ($categoryId) {
            $sql .= " AND (category_id = ? OR category_id IN (SELECT category_id FROM categories WHERE parent_id = ?))";
            $params[] = $categoryId;
            $params[] = $categoryId;
        }

        if ($cityId) {
            $sql .= " AND city_id = ?";
            $params[] = $cityId;
        }

        if ($itemConditionId) {
            $sql .= " AND item_condition_id = ?";
            $params[] = $itemConditionId;
        }

        if ($priceMin !== null && $priceMin != 0) {
            $sql .= " AND price >= ?";
            $params[] = $priceMin;
        }

        if ($priceMax !== null && $priceMax != 0) {
            $sql .= " AND price <= ?";
            $params[] = $priceMax;
        }

        if ($search) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
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

    public function batchDelete(array $ids): void
    {
        if (empty($ids)) return;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $this->db->delete('advertisements', "ad_id IN ({$placeholders})", $ids);
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
            "SELECT fn_next_image_sort_order(?) AS next",
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
