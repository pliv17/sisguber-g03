<?php

declare(strict_types=1);

namespace App\Controllers\Utilidades;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

/**
 * Integración / migración desde SIAF.
 */
final class MigracionSiafController
{
    use RendersStubPage;

    public function metas(Request $request): void
    {
        $this->renderStub(
            $request,
            'Migración SIAF — Metas',
            'Migra metas presupuestales',
            'Importación de metas desde SIAF.'
        );
    }

    public function fuentes(Request $request): void
    {
        $this->renderStub(
            $request,
            'Migración SIAF — Fuentes',
            'Migra fuentes de financiamiento',
            'Importación de fuentes desde SIAF.'
        );
    }

    public function partidas(Request $request): void
    {
        $this->renderStub(
            $request,
            'Migración SIAF — Partidas',
            'Migra partidas presupuestales',
            'Importación de partidas desde SIAF.'
        );
    }
}
