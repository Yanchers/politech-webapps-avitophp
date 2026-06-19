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

    public function create(array $data): Category
    {
        $id = $this->db->insert('categories', $data);
        return $this->findById($id);
    }

    public function update(int $id, array $data): void
    {
        $this->db->update('categories', $data, 'category_id = ?', [$id]);
    }

    public function delete(int $id): void
    {
        $this->db->delete('categories', 'category_id = ?', [$id]);
    }

    public function batchDelete(array $ids): void
    {
        if (empty($ids)) return;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $this->db->delete('categories', "category_id IN ({$placeholders})", $ids);
    }

    public function getTree(): array
    {
        $parents = $this->findParents();
        $tree = [];
        foreach ($parents as $parent) {
            $tree[] = [
                'parent' => $parent,
                'children' => $this->findByParentId($parent->category_id),
            ];
        }
        return $tree;
    }

    public function count(): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) AS cnt FROM categories");
        return (int) ($result['cnt'] ?? 0);
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
