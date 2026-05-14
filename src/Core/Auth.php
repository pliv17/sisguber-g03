<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Autenticación por sesión. Roles preparados en sesión (lista de strings).
 */
final class Auth
{
    public static function check(): bool
    {
        return Session::has('user_id') && Session::get('user_id') !== null;
    }

    public static function id(): ?int
    {
        $v = Session::get('user_id');
        return $v === null ? null : (int) $v;
    }

    /** @return list<string> */
    public static function roles(): array
    {
        $r = Session::get('roles', []);
        return is_array($r) ? array_values(array_map('strval', $r)) : [];
    }

    public static function hasRole(string $role): bool
    {
        return in_array($role, self::roles(), true);
    }

    /**
     * Modo demo: si AUTH_DEMO=true en .env, fija un usuario autenticado.
     */
    public static function ensureDemoUserIfEnabled(): void
    {
        if (!filter_var($_ENV['AUTH_DEMO'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            return;
        }
        if (self::check()) {
            return;
        }
        Session::set('user_id', 1);
        Session::set('roles', ['admin']);
    }
}
