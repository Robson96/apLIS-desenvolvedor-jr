<?php

namespace App\Core;

use App\Controllers\MedicoController;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        // Define routes
        $this->addRoute('GET', '/api/v1/medicos', MedicoController::class, 'index');
        $this->addRoute('POST', '/api/v1/medicos', MedicoController::class, 'store');
    }

    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            // Convert :id to regex group
            $pattern = preg_replace('/\:([a-zA-Z0-9_]+)/', '(?P<$1>[a-zA-Z0-9_-]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                // Filter matches to only string keys (named groups)
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                $controller = new $route['controller']();
                $action = $route['action'];
                call_user_func_array([$controller, $action], array_values($params));
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}
