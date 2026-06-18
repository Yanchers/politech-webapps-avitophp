<?php

namespace App\Models;

class Order
{
    public ?int $order_id = null;
    public ?string $order_number = null;
    public ?int $buyer_id = null;
    public ?float $total_amount = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public array $items = [];
}
