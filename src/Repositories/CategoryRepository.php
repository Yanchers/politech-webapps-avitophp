<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Category;

class CategoryRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?Category
    {
        $row = $this->db->fetch("SELECT * FROM categories WHERE category_id = ?", [$id]);
        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(?string $orderBy = null, string $direction = 'ASC'): array
    {
        $sql = "SELECT * FROM categories";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$direction}";
        } else {
            $sql .= " ORDER BY parent_id IS NULL DESC, parent_id ASC, name ASC";
        }
        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll($sql));
    }

    public function findParents(): array
    {
        return array_map(
            fn($row) => $this->hydrate($row),
            $this->db->fetchAll("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC")
        );
    }

    public function findByParentId(int $parentId): array
    {
        return array_map(
            fn($row) => $this->hydrate($row),
            $this->db->fetchAll("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC", [$parentId])
        );
    }

    private function hydrate(array $row): Category
    {
        $category = new Category();
        foreach ($row as $key => $value) {
            $category->$key = $value;
        }
        return $category;
    }
}
