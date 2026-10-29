<?php
declare(strict_types=1);

class Router
{
    /** @var array<int, array{method:string, path:string, handler:array}> */
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'path'    => '/' . trim($path, '/'),
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = current_path();
        $allowed = [];

        foreach ($this->routes as $route) {
            $params = $this->match($route['path'], $uri);
            if ($params === null) {
                continue;
            }

            $allowed[$route['method']] = true;
            if ($route['method'] !== $method) {
                continue;
            }

            [$class, $action] = $route['handler'];
            $controller = new $class();
            $controller->$action(...array_values($params));
            return;
        }

        if ($allowed !== []) {
            http_response_code(405);
            view('errors/404', ['title' => 'Metode tidak diizinkan'], null);
            return;
        }

        http_response_code(404);
        view('errors/404', ['title' => 'Halaman tidak ditemukan'], null);
    }

    /** @return array<string,string>|null */
    private function match(string $pattern, string $uri): ?array
    {
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }
}
