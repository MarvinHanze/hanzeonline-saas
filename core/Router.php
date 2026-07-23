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
                if (in_array($method, ['POST', 'PUT', 'DELETE'], true) && !Csrf::verify($_POST['_csrf'] ?? null)) {
                    $this->csrfFailed();
                    return;
                }
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->call($route['handler'], $params);
                return;
            }
        }

        // Een onbekend pad is vaak gewoon een module-URL die (nog) niet is
        // geregistreerd omdat de bezoeker niet is ingelogd (module-routes
        // worden pas geladen na authenticatie). Stuur in dat geval netjes
        // naar de inlogpagina i.p.v. een kale 404 te tonen.
        if (!Auth::isLoggedIn() && $uri !== '/login' && $uri !== '/register') {
            header('Location: ' . BASE . '/login');
            exit;
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    /** 403 bij een ontbrekend/ongeldig CSRF-token op een niet-GET request. */
    private function csrfFailed(): void
    {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="nl"><head><meta charset="UTF-8"><title>Ongeldige aanvraag</title></head>'
            . '<body style="font-family:sans-serif;padding:3rem;text-align:center;color:#334155">'
            . '<h1>403 — Ongeldige of verlopen aanvraag</h1>'
            . '<p>Deze actie kon niet worden geverifieerd (CSRF-token ontbreekt of is verlopen). '
            . 'Ga terug, ververs de pagina en probeer het opnieuw.</p>'
            . '<p><a href="javascript:history.back()">Terug</a></p></body></html>';
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
