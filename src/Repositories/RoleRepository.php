<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Role;

class RoleRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?Role
    {
        $row = $this->db->fetch("SELECT * FROM roles WHERE role_id = ?", [$id]);
        return $row ? $this->hydrate($row) : null;
    }

    public function findByName(string $name): ?Role
    {
        $row = $this->db->fetch("SELECT * FROM roles WHERE name = ?", [$name]);
        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll("SELECT * FROM roles"));
    }

    private function hydrate(array $row): Role
    {
        $role = new Role();
        foreach ($row as $key => $value) {
            $role->$key = $value;
        }
        return $role;
    }
}
