<?php

namespace App\Core;

abstract class Controller
{
    protected Request $request;
    protected Session $session;
    protected View $view;

    public function __construct()
    {
        $this->request = new Request();
        $this->session = Session::getInstance();
        $this->view = new View();
    }

    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    protected function back(): void
    {
        $referer = $this->request->header('Referer') ?? '/';
        $this->redirect($referer);
    }

    protected function getLayoutData(): array
    {
        return [
            'user' => $this->session->getUser(),
            'app' => require __DIR__ . '/../../config/app.php',
        ];
    }

    protected function render(string $template, array $data = []): void
    {
        $this->view->render($template, array_merge($this->getLayoutData(), $data));
    }

    protected function renderAdmin(string $template, array $data = []): void
    {
        $this->view->renderAdmin($template, array_merge($this->getLayoutData(), $data));
    }
}
