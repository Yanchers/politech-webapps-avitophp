<?php

namespace App\Core;

class View
{
    private string $viewsPath;

    public function __construct(?string $viewsPath = null)
    {
        $this->viewsPath = $viewsPath ?: __DIR__ . '/../Views';
    }

    public function render(string $template, array $data = []): void
    {
        extract($data);

        $layoutContent = function () use ($template, $data) {
            extract($data);
            $path = $this->viewsPath . '/' . $template . '.php';
            if (!file_exists($path)) {
                throw new \RuntimeException("View not found: {$template}");
            }
            require $path;
        };

        require $this->viewsPath . '/layouts/header.php';
        $layoutContent();
        require $this->viewsPath . '/layouts/footer.php';
    }

    public function renderAdmin(string $template, array $data = []): void
    {
        extract($data);

        $layoutContent = function () use ($template, $data) {
            extract($data);
            $path = $this->viewsPath . '/' . $template . '.php';
            if (!file_exists($path)) {
                throw new \RuntimeException("View not found: {$template}");
            }
            require $path;
        };

        require $this->viewsPath . '/layouts/admin_header.php';
        $layoutContent();
        require $this->viewsPath . '/layouts/footer.php';
    }

    public function renderPartial(string $template, array $data = []): void
    {
        extract($data);
        $path = $this->viewsPath . '/' . $template . '.php';
        if (!file_exists($path)) {
            throw new \RuntimeException("View not found: {$template}");
        }
        require $path;
    }

    public function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }


    public function buildSearchUrl(array $overrides = []): string
    {
        $params = $_GET;
        foreach ($overrides as $key => $value) {
            if ($value === null || $value === '') {
                unset($params[$key]);
            } else {
                $params[$key] = $value;
            }
        }
        $query = http_build_query($params);
        return '/search' . ($query ? '?' . $query : '');
    }
}
