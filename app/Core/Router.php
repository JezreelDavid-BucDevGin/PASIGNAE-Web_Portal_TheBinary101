<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->normalize($path),
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): void
    {
        $uri = $this->normalize($request->uri());
        $method = $request->method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['path'], $uri);
            if ($params === false) {
                continue;
            }

            foreach ($route['middleware'] as $mw) {
                $instance = new $mw();
                $instance->handle($request);
            }

            [$controller, $action] = $route['handler'];
            $instance = new $controller();
            call_user_func_array([$instance, $action], $params);
            return;
        }

        http_response_code(404);
        view('errors.404', ['layout' => 'guest']);
    }

    private function normalize(string $path): string
    {
        return '/' . trim($path, '/');
    }

    private function match(string $routePath, string $uri): array|false
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) {
            return false;
        }

        array_shift($matches);
        return $matches;
    }
}
