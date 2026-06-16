<?php

namespace App\Core;

class Session
{
    private static ?Session $instance = null;
    private ?array $user = null;
    private ?int $userId = null;
    private ?string $sessionToken = null;

    private function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->loadFromToken();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function login(int $userId, string $roleName): void
    {
        $this->logout();

        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 604800);

        $db = Database::getInstance();
        $db->insert('user_sessions', [
            'user_id' => $userId,
            'token_hash' => $hash,
            'expires_at' => $expiresAt,
        ]);

        setcookie('session_token', $token, time() + 604800, '/', '', false, true);

        $this->userId = $userId;
        $this->sessionToken = $token;
    }

    public function logout(): void
    {
        $token = $_COOKIE['session_token'] ?? $this->sessionToken;
        if ($token) {
            $db = Database::getInstance();
            $db->delete('user_sessions', 'token_hash = ?', [hash('sha256', $token)]);
            setcookie('session_token', '', time() - 3600, '/', '', false, true);
        }

        $this->userId = null;
        $this->user = null;
        $this->sessionToken = null;
    }

    public function isAuthenticated(): bool
    {
        return $this->userId !== null;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUser(): ?array
    {
        if ($this->user === null && $this->userId !== null) {
            $db = Database::getInstance();
            $this->user = $db->fetch(
                "SELECT u.*, r.name AS role_name
                 FROM users u
                 JOIN roles r ON u.role_id = r.role_id
                 WHERE u.user_id = ?",
                [$this->userId]
            );
        }
        return $this->user;
    }

    public function getUserRole(): ?string
    {
        $user = $this->getUser();
        return $user['role_name'] ?? null;
    }

    public function setFlash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    private function loadFromToken(): void
    {
        $token = $_COOKIE['session_token'] ?? null;
        if (!$token) {
            return;
        }

        $db = Database::getInstance();
        $row = $db->fetch(
            "SELECT user_id FROM user_sessions WHERE token_hash = ? AND expires_at > NOW()",
            [hash('sha256', $token)]
        );

        if ($row) {
            $this->userId = (int) $row['user_id'];
            $this->sessionToken = $token;
        } else {
            setcookie('session_token', '', time() - 3600, '/', '', false, true);
        }
    }
}
