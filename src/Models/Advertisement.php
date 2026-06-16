<?php

namespace App\Models;

class Advertisement
{
    public ?int $ad_id = null;
    public ?int $seller_id = null;
    public ?int $category_id = null;
    public ?int $item_condition_id = null;
    public ?int $city_id = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?float $price = null;
    public ?int $status_id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
}
