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

    public function findAllSimple(): array
    {
        return $this->db->fetchAll("SELECT user_id, email, first_name, last_name FROM users ORDER BY user_id ASC");
    }

    public function findAllWithRolesAndCities(): array
    {
        return $this->db->fetchAll("SELECT * FROM user_view ORDER BY user_id ASC");
    }

    public function create(array $data): User
    {
        $id = $this->db->insert('users', $data);
        return $this->findById($id);
    }

    public function update(int $id, array $data): void
    {
        $this->db->update('users', $data, 'user_id = ?', [$id]);
    }

    public function delete(int $id): void
    {
        $this->db->delete('users', 'user_id = ?', [$id]);
    }

    public function batchDelete(array $ids): void
    {
        if (empty($ids)) return;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $this->db->delete('users', "user_id IN ({$placeholders})", $ids);
    }

    public function count(): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) AS cnt FROM users");
        return (int) ($result['cnt'] ?? 0);
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
