<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Support;

final class RucValidator
{
    /**
     * RUC Perú: 11 dígitos numéricos (validación de forma; no checksum SUNAT).
     */
    public static function isValid(string $ruc): bool
    {
        $ruc = trim($ruc);
        if (strlen($ruc) !== 11 || !ctype_digit($ruc)) {
            return false;
        }

        return true;
    }
}
