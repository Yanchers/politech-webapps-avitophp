<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\City;

class CityRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?City
    {
        $row = $this->db->fetch("SELECT * FROM cities WHERE city_id = ?", [$id]);
        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(?string $orderBy = null, string $direction = 'ASC'): array
    {
        $sql = "SELECT * FROM cities";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll($sql));
    }

    public function create(string $name): City
    {
        $id = $this->db->insert('cities', ['name' => $name]);
        return $this->findById($id);
    }

    public function update(int $id, string $name): void
    {
        $this->db->update('cities', ['name' => $name], 'city_id = ?', [$id]);
    }

    public function delete(int $id): void
    {
        $this->db->delete('cities', 'city_id = ?', [$id]);
    }

    public function batchDelete(array $ids): void
    {
        if (empty($ids)) return;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $this->db->delete('cities', "city_id IN ({$placeholders})", $ids);
    }

    public function count(): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) AS cnt FROM cities");
        return (int) ($result['cnt'] ?? 0);
    }

    private function hydrate(array $row): City
    {
        $city = new City();
        foreach ($row as $key => $value) {
            $city->$key = $value;
        }
        return $city;
    }
}
