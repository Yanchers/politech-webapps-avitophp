<?php

namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class RoleMiddleware
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(): void
    {
        $session = Session::getInstance();
        $role = $session->getUserRole();

        if ($role === null || !in_array($role, $this->allowedRoles, true)) {
            Response::forbidden();
        }
    }
}
