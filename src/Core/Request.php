<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Request — Encapsula la petición HTTP entrante.
 * Nunca uses $_GET/$_POST directamente en controladores; usa esta clase.
 */
class Request
{
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * URI sin query string.
     */
    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        // Eliminar query string
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        return rawurldecode($uri);
    }

    /**
     * Parámetro GET saneado.
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
    }

    /**
     * Parámetro POST saneado.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : $default;
    }

    /**
     * Cuerpo JSON decodificado (para peticiones Ajax JSON).
     */
    public function json(): mixed
    {
        $body = file_get_contents('php://input');
        return json_decode($body ?: '{}', true);
    }

    /**
     * ¿Es una petición XMLHttpRequest (Ajax)?
     */
    public function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * Sanea un valor escalar. Arrays se procesan recursivamente.
     * IMPORTANTE: No uses esto para HTML — usa htmlspecialchars() en las vistas.
     */
    private function sanitize(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }
        return is_string($value) ? trim(strip_tags($value)) : $value;
    }
}
