<?php

namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class GuestMiddleware
{
    public function handle(): void
    {
        $session = Session::getInstance();
        if ($session->isAuthenticated()) {
            Response::redirect('/');
        }
    }
}
