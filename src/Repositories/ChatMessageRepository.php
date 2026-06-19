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

    public function findByAdAndUsers(int $adId, int $userId1, int $userId2): array
    {
        $sql = "SELECT * FROM ad_chat_messages 
                WHERE ad_id = ? 
                AND ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
                ORDER BY ad_chat_message_id ASC";
        return array_map(
            fn($row) => $this->hydrate($row),
            $this->db->fetchAll($sql, [$adId, $userId1, $userId2, $userId2, $userId1])
        );
    }

    public function getUserChats(int $userId): array
    {
        $sql = "SELECT 
                    m.*,
                    u.user_id AS other_user_id,
                    u.first_name AS other_first_name,
                    u.last_name AS other_last_name,
                    a.title AS ad_title,
                    ai.image_path AS first_image
                FROM ad_chat_messages m
                JOIN users u ON u.user_id IN (m.sender_id, m.receiver_id) AND u.user_id != ?
                JOIN advertisements a ON a.ad_id = m.ad_id
                LEFT JOIN advertisement_images ai ON ai.ad_id = a.ad_id AND ai.sort_order = 1
                WHERE m.ad_chat_message_id IN (
                    SELECT MAX(ad_chat_message_id)
                    FROM ad_chat_messages
                    WHERE ? IN (sender_id, receiver_id)
                    GROUP BY ad_id, LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id) -- нормализация пары, иначе будет несколько строк (1 -> 2, 2 -> 1)
                )
                ORDER BY m.ad_chat_message_id DESC";
        return $this->db->fetchAll($sql, [$userId, $userId]);
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
