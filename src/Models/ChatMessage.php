<?php

namespace App\Models;

class ChatMessage
{
    public ?int $ad_chat_message_id = null;
    public ?int $ad_id = null;
    public ?int $sender_id = null;
    public ?int $receiver_id = null;
    public ?string $message = null;
    public ?string $created_at = null;
}
