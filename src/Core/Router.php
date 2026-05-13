<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router — Enrutador ligero para el sistema de abastecimiento.
 *
 * Uso en routes/web.php:
 *   $router->get('/ruta', [MiController::class, 'metodo']);
 *   $router->post('/ruta', [MiController::class, 'metodo']);
 */
class Router
{
    /** @var array<string, array<string, array{0: string, 1: string}>> */
    private array $routes = [];

    /**
     * Registra una ruta GET.
     *
     * @param string $uri
     * @param array{0: class-string, 1: string} $action [ControllerClass, 'method']
     */
    public function get(string $uri, array $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Registra una ruta POST.
     */
    public function post(string $uri, array $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    private function addRoute(string $method, string $uri, array $action): void
    {
        $this->routes[$method][rtrim($uri, '/') ?: '/'] = $action;
    }

    /**
     * Despacha la petición al controlador correspondiente.
     */
    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri    = rtrim($request->uri(), '/') ?: '/';

        $action = $this->routes[$method][$uri] ?? null;

        if ($action === null) {
            $this->notFound();
            return;
        }

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
