<?php

namespace App\Models;

class OrderItem
{
    public ?int $order_item_id = null;
    public ?int $order_id = null;
    public ?int $ad_id = null;
    public ?float $price_paid = null;
    public ?string $ad_title = null;
    public ?string $seller_first_name = null;
    public ?string $seller_last_name = null;
    public ?string $seller_email = null;
    public ?string $seller_phone = null;
    public ?string $first_image_path = null;
}
