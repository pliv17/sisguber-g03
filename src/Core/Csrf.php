<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Token CSRF en sesión + cabecera / cuerpo JSON.
 */
final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (!Session::has(self::SESSION_KEY) || !is_string(Session::get(self::SESSION_KEY))) {
            Session::set(self::SESSION_KEY, bin2hex(random_bytes(32)));
        }

        return (string) Session::get(self::SESSION_KEY);
    }

    public static function verifyFromRequest(Request $request): bool
    {
        $expected = Session::get(self::SESSION_KEY);
        if (!is_string($expected) || $expected === '') {
            return false;
        }

        $header = $request->header('X-CSRF-TOKEN');
        if (is_string($header) && hash_equals($expected, $header)) {
            return true;
        }

        if ($request->isJson()) {
            $json = $request->json();
            if (is_array($json) && isset($json['csrf_token']) && is_string($json['csrf_token'])) {
                return hash_equals($expected, $json['csrf_token']);
            }
        }

        $input = $request->input('csrf_token');
        if (is_string($input)) {
            return hash_equals($expected, $input);
        }

        return false;
    }
}
