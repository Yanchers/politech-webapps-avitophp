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

    private function hydrate(array $row): ItemCondition
    {
        $condition = new ItemCondition();
        foreach ($row as $key => $value) {
            $condition->$key = $value;
        }
        return $condition;
    }
}
