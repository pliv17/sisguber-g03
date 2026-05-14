<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Request — Encapsula la petición HTTP entrante.
 */
class Request
{
    /** @var array<string, string> */
    private array $routeParams = [];

    /**
     * @param array<string, string> $params
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function route(string $key, ?string $default = null): ?string
    {
        return $this->routeParams[$key] ?? $default;
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * URI sin query string, relativa al prefijo de ruta de APP_URL (si existe).
     */
    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $path = rawurldecode($uri);

        $appUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        $basePath = $appUrl !== '' ? (parse_url($appUrl, PHP_URL_PATH) ?: '') : '';
        $basePath = rtrim((string) $basePath, '/');
        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        return rtrim($path, '/') ?: '/';
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : $default;
    }

    /**
     * Cabecera HTTP (comparación case-insensitive).
     */
    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        foreach ($_SERVER as $k => $v) {
            if (strtoupper($k) === $key && is_string($v)) {
                return $v;
            }
        }
        return null;
    }

    public function isJson(): bool
    {
        $ct = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
        return is_string($ct) && str_contains(strtolower($ct), 'application/json');
    }

    /**
     * Cuerpo JSON decodificado (array). Si no es JSON válido, devuelve [].
     *
     * @return array<string, mixed>
     */
    public function json(): array
    {
        $body = (string) file_get_contents('php://input');
        if ($body === '') {
            return [];
        }
        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }

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

    public function isPut(): bool
    {
        return $this->method() === 'PUT';
    }

    public function isPatch(): bool
    {
        return $this->method() === 'PATCH';
    }

    public function isDelete(): bool
    {
        return $this->method() === 'DELETE';
    }

    private function sanitize(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }

        return is_string($value) ? trim(strip_tags($value)) : $value;
    }
}
