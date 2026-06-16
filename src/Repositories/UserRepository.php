<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;

class UserRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?User
    {
        $row = $this->db->fetch("SELECT * FROM users WHERE user_id = ?", [$id]);
        return $row ? $this->hydrate($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $row = $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll("SELECT * FROM users"));
    }

    public function create(array $data): User
    {
        $id = $this->db->insert('users', $data);
        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        $this->db->delete('users', 'user_id = ?', [$id]);
    }

    public function hydrate(array $row): User
    {
        $user = new User();
        foreach ($row as $key => $value) {
            $user->$key = $value;
        }
        return $user;
    }
}
