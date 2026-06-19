<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\ItemCondition;

class ItemConditionRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?ItemCondition
    {
        $row = $this->db->fetch("SELECT * FROM item_conditions WHERE item_condition_id = ?", [$id]);
        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll("SELECT * FROM item_conditions ORDER BY item_condition_id ASC"));
    }

    public function create(string $name): ItemCondition
    {
        $id = $this->db->insert('item_conditions', ['name' => $name]);
        return $this->findById($id);
    }

    public function update(int $id, string $name): void
    {
        $this->db->update('item_conditions', ['name' => $name], 'item_condition_id = ?', [$id]);
    }

    public function delete(int $id): void
    {
        $this->db->delete('item_conditions', 'item_condition_id = ?', [$id]);
    }

    public function batchDelete(array $ids): void
    {
        if (empty($ids)) return;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $this->db->delete('item_conditions', "item_condition_id IN ({$placeholders})", $ids);
    }

    public function count(): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) AS cnt FROM item_conditions");
        return (int) ($result['cnt'] ?? 0);
    }

    private function hydrate(array $row): ItemCondition
    {
        $condition = new ItemCondition();
        foreach ($row as $key => $value) {
            $condition->$key = $value;
        }
        return $condition;
    }
}
