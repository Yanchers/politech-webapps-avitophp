<?php

namespace App\Models;

class Advertisement
{
    const STATUS_DRAFT = 1;
    const STATUS_MODERATION = 2;
    const STATUS_ACTIVE = 3;
    const STATUS_REJECTED = 4;
    const STATUS_SOLD = 5;
    const STATUS_DELETED = 6;

    const EDITABLE_STATUSES = [self::STATUS_DRAFT, self::STATUS_MODERATION, self::STATUS_ACTIVE, self::STATUS_REJECTED];
    const STATUSES_REQUIRING_REMODERATION = [self::STATUS_ACTIVE, self::STATUS_REJECTED];
    const STATUSES_FORBIDDEN = [self::STATUS_SOLD, self::STATUS_DELETED];

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
