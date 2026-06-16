<?php

namespace App\Core;

class Response
{
    public static function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    public static function setHeader(string $name, string $value): void
    {
        header("{$name}: {$value}");
    }

    public static function notFound(): void
    {
        http_response_code(404);
        $view = new View();
        $view->render('errors/404');
        exit;
    }

    public static function forbidden(): void
    {
        http_response_code(403);
        $view = new View();
        $view->render('errors/403');
        exit;
    }
}
