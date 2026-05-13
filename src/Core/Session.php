<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Session — Gestión segura de sesiones PHP.
 *
 * Características:
 *   - Cookie httponly (no accesible desde JS)
 *   - SameSite=Strict (previene CSRF básico)
 *   - Secure solo cuando APP_URL usa HTTPS
 *   - Regeneración de ID en login (ver regenerate())
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $secure   = str_starts_with($_ENV['APP_URL'] ?? '', 'https');
        $lifetime = 0; // hasta que el navegador se cierre

        session_name($_ENV['SESSION_NAME'] ?? 'abastecimiento_session');

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => true,       // ← Protege contra XSS que robe la cookie
            'samesite' => 'Strict',   // ← Mitiga CSRF
        ]);

        session_start();
    }

    /**
     * Regenera el ID de sesión (llamar tras login exitoso).
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Flash: guarda un mensaje para la siguiente petición.
     */
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
}
