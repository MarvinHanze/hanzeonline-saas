<?php
declare(strict_types=1);

namespace Core;

class Router
{
    private array $routes = [];
    private string $prefix = '';

    public function group(string $prefix, callable $callback): void
    {
        $oldPrefix = $this->prefix;
        $this->prefix = $oldPrefix . $prefix;
        $callback($this);
        $this->prefix = $oldPrefix;
    }

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->prefix . $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            $pattern = $this->toRegex($route['path']);
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->call($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private function call(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $controller->$method(...array_values($params));
        } else {
            $handler(...array_values($params));
        }
    }

    private function toRegex(string $path): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
