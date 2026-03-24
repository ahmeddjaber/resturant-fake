<?php

namespace Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $uri, array $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, array $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    private function addRoute(string $method, string $uri, array $action): void
    {
        $this->routes[$method][$this->normalizeUri($uri)] = $action;
    }

    public function dispatch(string $uri, string $method): void
    {
        $normalizedUri = $this->normalizeUri($uri);
        $httpMethod = strtoupper($method);
        $route = $this->routes[$httpMethod][$normalizedUri] ?? null;

        if ($route === null) {
            if ($this->routeExistsForAnotherMethod($normalizedUri, $httpMethod)) {
                $this->sendMethodNotAllowed();
                return;
            }

            $this->sendNotFound();
            return;
        }

        [$controllerClass, $controllerMethod] = $route;

        if (!class_exists($controllerClass) || !method_exists($controllerClass, $controllerMethod)) {
            $this->sendServerError('Route handler is not correctly configured.');
            return;
        }

        $controller = new $controllerClass();
        $controller->{$controllerMethod}();
    }

    private function normalizeUri(string $uri): string
    {
        $cleanUri = parse_url($uri, PHP_URL_PATH) ?? '/';
        $cleanUri = '/' . trim($cleanUri, '/');

        return $cleanUri === '//' ? '/' : $cleanUri;
    }

    private function routeExistsForAnotherMethod(string $uri, string $currentMethod): bool
    {
        foreach ($this->routes as $method => $routes) {
            if ($method === $currentMethod) {
                continue;
            }

            if (isset($routes[$uri])) {
                return true;
            }
        }

        return false;
    }

    private function sendNotFound(): void
    {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Route not found.',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function sendMethodNotAllowed(): void
    {
        http_response_code(405);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed for this route.',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function sendServerError(string $message): void
    {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}