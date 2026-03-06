<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $action, $middleware);
    }

    public function post(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function patch(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('PATCH', $path, $action, $middleware);
    }

    public function delete(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $action, $middleware);
    }

    private function addRoute(string $method, string $path, string $action, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        // Method spoofing for HTML forms
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = parse_url($uri, PHP_URL_PATH);
        $basePath = parse_url(\App\Config\App::baseUrl(), PHP_URL_PATH);
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . trim($uri, '/');
        if ($uri === '/') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            $params = $this->matchRoute($route['path'], $uri);
            if ($params !== false && $route['method'] === $method) {
                // Run middleware
                foreach ($route['middleware'] as $mw) {
                    $middlewareClass = "App\\Middleware\\{$mw}";
                    if (class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        $middlewareInstance->handle();
                    }
                }

                // Parse controller@method
                [$controllerName, $methodName] = explode('@', $route['action']);
                $controllerClass = "App\\Controllers\\{$controllerName}";

                if (!class_exists($controllerClass)) {
                    $this->sendError(500, "Controller not found: {$controllerClass}");
                    return;
                }

                $controller = new $controllerClass();
                if (!method_exists($controller, $methodName)) {
                    $this->sendError(500, "Method not found: {$methodName}");
                    return;
                }

                call_user_func_array([$controller, $methodName], array_values($params));
                return;
            }
        }

        $this->sendError(404, 'Trang không tồn tại');
    }

    private function matchRoute(string $routePath, string $uri): array|false
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $params = [];
        for ($i = 0; $i < count($routeParts); $i++) {
            if (preg_match('/^\{(\w+)\}$/', $routeParts[$i], $matches)) {
                $params[$matches[1]] = $uriParts[$i];
            } elseif ($routeParts[$i] !== $uriParts[$i]) {
                return false;
            }
        }

        return $params;
    }

    private function sendError(int $code, string $message): void
    {
        http_response_code($code);
        if ($code === 404) {
            include dirname(__DIR__) . '/Views/errors/404.php';
        } else {
            echo "<h1>Error {$code}</h1><p>" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</p>";
        }
    }
}
