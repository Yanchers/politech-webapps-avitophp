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

    private function hydrate(array $row): City
    {
        $city = new City();
        foreach ($row as $key => $value) {
            $city->$key = $value;
        }
        return $city;
    }
}
