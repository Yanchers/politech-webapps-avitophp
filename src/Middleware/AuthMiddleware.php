<?php

namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(): void
    {
        $session = Session::getInstance();
        if (!$session->isAuthenticated()) {
            $session->setFlash('error', 'Необходимо авторизоваться');
            Response::redirect('/login');
        }
    }
}
