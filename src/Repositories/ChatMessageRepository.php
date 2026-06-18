<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\ChatMessage;

class ChatMessageRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?ChatMessage
    {
        $row = $this->db->fetch("SELECT * FROM ad_chat_messages WHERE ad_chat_message_id = ?", [$id]);
        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        return array_map(fn($row) => $this->hydrate($row), $this->db->fetchAll("SELECT * FROM ad_chat_messages ORDER BY ad_chat_message_id ASC"));
    }

    public function create(array $data): ChatMessage
    {
        $id = $this->db->insert('ad_chat_messages', $data);
        return $this->findById($id);
    }

    public function update(int $id, array $data): void
    {
        $this->db->update('ad_chat_messages', $data, 'ad_chat_message_id = ?', [$id]);
    }

    public function delete(int $id): void
    {
        $this->db->delete('ad_chat_messages', 'ad_chat_message_id = ?', [$id]);
    }

    private function hydrate(array $row): ChatMessage
    {
        $message = new ChatMessage();
        foreach ($row as $key => $value) {
            $message->$key = $value;
        }
        return $message;
    }
}
