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
