<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router — Enrutador con rutas estáticas y dinámicas `{param}`.
 *
 * Las rutas con `{nombre}` se resuelven como `[^/]+`. Los parámetros se
 * inyectan en {@see Request::setRouteParams()} antes de llamar al controlador.
 */
class Router
{
    /** @var array<string, array<string, array{0: string, 1: string}>> */
    private array $staticRoutes = [];

    /**
     * @var array<string, list<array{regex: string, paramNames: list<string>, action: array{0: string, 1: string}}>>
     */
    private array $dynamicRoutes = [];

    public function get(string $uri, array $action): void
    {
        $this->add('GET', $uri, $action);
    }

    public function post(string $uri, array $action): void
    {
        $this->add('POST', $uri, $action);
    }

    public function put(string $uri, array $action): void
    {
        $this->add('PUT', $uri, $action);
    }

    public function patch(string $uri, array $action): void
    {
        $this->add('PATCH', $uri, $action);
    }

    public function delete(string $uri, array $action): void
    {
        $this->add('DELETE', $uri, $action);
    }

    /**
     * @param array{0: class-string, 1: string} $action
     */
    private function add(string $method, string $uri, array $action): void
    {
        $uri = rtrim($uri, '/') ?: '/';
        if (str_contains($uri, '{')) {
            [$regex, $names] = $this->compilePattern($uri);
            $this->dynamicRoutes[$method][] = [
                'regex'      => $regex,
                'paramNames' => $names,
                'action'     => $action,
            ];
            return;
        }
        $this->staticRoutes[$method][$uri] = $action;
    }

    /**
     * @return array{0: string, 1: list<string>}
     */
    private function compilePattern(string $uri): array
    {
        $names = [];
        $regexBody = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (array $m) use (&$names): string {
                $names[] = $m[1];

                return '(?P<' . $m[1] . '>[^/]+)';
            },
            $uri
        );

        return ['#^' . $regexBody . '$#u', $names];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri     = rtrim($request->uri(), '/') ?: '/';

        $request->setRouteParams([]);

        if (isset($this->staticRoutes[$method][$uri])) {
            $this->invoke($request, $this->staticRoutes[$method][$uri]);
            return;
        }

        foreach ($this->dynamicRoutes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $uri, $matches)) {
                $params = [];
                foreach ($route['paramNames'] as $name) {
                    $params[$name] = $matches[$name] ?? ($matches[1] ?? null);
                }
                $request->setRouteParams($params);
                $this->invoke($request, $route['action']);
                return;
            }
        }

        $this->notFound();
    }

    /**
     * @param array{0: string, 1: string} $action
     */
    private function invoke(Request $request, array $action): void
    {
        [$controllerClass, $methodName] = $action;

        if (!class_exists($controllerClass)) {
            $this->serverError("Controlador no encontrado: {$controllerClass}");
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            $this->serverError("Método no encontrado: {$controllerClass}::{$methodName}");
            return;
        }

        $controller->$methodName($request);
    }

    private function notFound(): void
    {
        http_response_code(404);
        $title   = '404 – Página no encontrada';
        $message = 'La ruta solicitada no existe.';
        require BASE_PATH . '/views/errors/error.php';
    }

    private function serverError(string $detail): void
    {
        http_response_code(500);
        $title   = '500 – Error interno';
        $message = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN)
            ? $detail
            : 'Error interno del servidor.';
        require BASE_PATH . '/views/errors/error.php';
    }
}
