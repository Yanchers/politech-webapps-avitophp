<?php

namespace App\Core;

// класс маршрутизатора, обрабатывает HTTP запросы и напрвляет их к нужным контроллерам
class Router
{
    private array $routes = []; // зарегистрированные маршруты
    private array $middlewareAliases = []; // массив сокращений для middleware (например 'auth' -> объект AuthMiddleware)
    private array $globalMiddleware = []; // массив middleware, которые выполняются для всех маршрутов

    public function addMiddlewareAlias(string $alias, callable $factory): void
    {
        $this->middlewareAliases[$alias] = $factory;
    }

    public function addGlobalMiddleware(callable $factory): void
    {
        $this->globalMiddleware[] = $factory;
    }

    public function get(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, string $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $pattern, $handler, $middleware);
    }
    // добавляет маршрут в массив $routes. 
    public function addRoute(string $method, string $pattern, string $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH); // извлекаем только путь из URL (убираем query и фрагменты - /home?id=#section -> /home)
        $uri = rtrim($uri, '/') ?: '/'; // убрать слеш в конце или вернуть просто корневой путь

        foreach ($this->globalMiddleware as $factory) {
            $factory()->handle();
        }
        // проходим по каждому зарегистрированному маршруту чтобы найти нужный нам
        foreach ($this->routes as $route) {
            // если не совпадает метод - скип
            if ($route['method'] !== $method) {
                continue;
            }
            // проверяем совпадает ли текущий URL с паттерном маршрута
            // если да - возвращает параметры (['id' => '5'] для паттерна /posts/{id})
            // если нет - возвращает null
            $params = $this->matchRoute($route['pattern'], $uri);
            if ($params === null) {
                continue;
            }
            // выполнить middlewares связанные с этим маршрутом
            $this->runMiddleware($route['middleware']);
            // разбиваем машрут на контроллер и страницу
            [$controllerClass, $action] = explode('@', $route['handler']);
            $controllerClass = 'App\\Controllers\\' . $controllerClass;

            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller {$controllerClass} not found");
            }

            $controller = new $controllerClass();
            $controller->$action(...$params);
            return;
        }

        $this->handleNotFound();
    }

    private function matchRoute(string $pattern, string $uri): ?array
    {
        // преобразование паттерна в регулярку  (/posts/{id} → /posts/(?P<id>[^/]+))
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        // проверяет, совпадает ли URL с регулярным выражением
        if (preg_match($regex, $uri, $matches)) {
            // оставялем только строковые ключи (ARRAY_FILTER_USE_KEY - передает в callback только ключ)
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return null;
    }

    private function runMiddleware(array $middlewareList): void
    {
        foreach ($middlewareList as $item) {
            // если middleware существует в $middlewareAliases то мы его вызываем
            if (isset($this->middlewareAliases[$item])) {
                $middleware = call_user_func($this->middlewareAliases[$item]);
                $middleware->handle();
            }
            //  elseif (class_exists($item)) { // если полное имя класса | все что ниже я думаю лишнее? =)
            //     $middleware = new $item();
            //     $middleware->handle();
            elseif (str_starts_with($item, 'role:')) { // это проверка роли?
                $roles = explode(',', substr($item, 5));
                $middleware = new \App\Middleware\RoleMiddleware($roles);
                $middleware->handle();
            }
        }
    }

    private function handleNotFound(): void
    {
        // устанавливем HTTP статус 404, и отрисовываем шаблон errors/404.php
        http_response_code(404);
        $view = new View();
        $view->render('errors/404', ['title' => '404 — Страница не найдена']);
    }
}
