<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Response — Helpers para enviar respuestas HTTP.
 */
class Response
{
    /**
     * Envía una respuesta JSON y termina la ejecución.
     */
    public static function json(mixed $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Redirige a otra URL y termina.
     */
    public static function redirect(string $url, int $statusCode = 302): never
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Renderiza una vista PHP (no hace echo; usa include).
     *
     * @param string $view    Ruta relativa a views/, ej: 'home/index'
     * @param array  $data    Variables disponibles en la vista
     */
    public static function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Vista no encontrada: {$viewPath}");
        }
        require $viewPath;
    }
}
