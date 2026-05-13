<?php

declare(strict_types=1);

/**
 * helpers.php — Funciones globales de utilidad para vistas y controladores.
 *
 * Incluido automáticamente vía Composer (si se configura en autoload.files)
 * o manualmente desde bootstrap.php.
 */

if (!function_exists('e')) {
    /**
     * Escapa HTML para salida segura en vistas.
     * SIEMPRE usa esta función al imprimir datos en HTML.
     *
     * Ejemplo: <td><?= e($row['nombre']) ?></td>
     *
     * @param  mixed  $value
     * @return string
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    /**
     * Genera URL absoluta para un asset en public/assets/.
     *
     * Ejemplo: <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
     */
    function asset(string $path): string
    {
        $base = rtrim($_ENV['APP_URL'] ?? '', '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Genera URL absoluta de la aplicación.
     *
     * Ejemplo: url('/maestros/almacenes')
     */
    function url(string $path = ''): string
    {
        $base = rtrim($_ENV['APP_URL'] ?? '', '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('request_path')) {
    /**
     * Ruta actual normalizada (misma lógica que {@see \App\Core\Request::uri()}).
     */
    function request_path(): string
    {
        return (new \App\Core\Request())->uri();
    }
}

if (!function_exists('nav_is_active_path')) {
    function nav_is_active_path(string $path): bool
    {
        $path = rtrim($path, '/') ?: '/';
        return request_path() === $path;
    }
}

if (!function_exists('nav_is_active_prefix')) {
    function nav_is_active_prefix(string $prefix): bool
    {
        $current = request_path();
        $prefix = rtrim($prefix, '/');
        if ($prefix === '' || $prefix === '/') {
            return $current === '/';
        }

        return $current === $prefix || str_starts_with($current, $prefix . '/');
    }
}

if (!function_exists('nav_is_active_any_prefix')) {
    /** @param list<string> $prefixes */
    function nav_is_active_any_prefix(array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if (nav_is_active_prefix($prefix)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('nav_active')) {
    /** @return 'active'|'' */
    function nav_active(string $path): string
    {
        return nav_is_active_path($path) ? 'active' : '';
    }
}

if (!function_exists('nav_dd_active')) {
    /**
     * Marca el toggle de un dropdown cuando la ruta actual cae bajo alguno de los prefijos.
     *
     * @param list<string> $prefixes
     * @return 'active'|''
     */
    function nav_dd_active(array $prefixes): string
    {
        return nav_is_active_any_prefix($prefixes) ? 'active' : '';
    }
}

if (!function_exists('nav_context_title')) {
    /**
     * Título corto para la barra de contexto (quita sufijo "— Sistema…").
     */
    function nav_context_title(?string $pageTitle): string
    {
        if ($pageTitle === null || $pageTitle === '') {
            return '';
        }

        return trim(preg_replace('/\s+[—–-]\s*Sistema.*$/iu', '', $pageTitle));
    }
}

if (!function_exists('dd')) {
    /**
     * Dump & Die — Solo para depuración (no usar en producción).
     */
    function dd(mixed ...$vars): never
    {
        foreach ($vars as $var) {
            echo '<pre style="background:#1e1e1e;color:#d4d4d4;padding:1rem;margin:1rem;border-radius:4px;">';
            echo e(print_r($var, true));
            echo '</pre>';
        }
        exit;
    }
}
