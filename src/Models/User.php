<?php

namespace App\Models;

class User
{
    public ?int $user_id = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $password_hash = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?int $role_id = null;
    public ?int $city_id = null;
    public ?string $created_at = null;
}
