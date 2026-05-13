<?php

declare(strict_types=1);

namespace App\Controllers\Utilidades;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

final class RespaldoController
{
    use RendersStubPage;

    public function index(Request $request): void
    {
        $this->renderStub(
            $request,
            'Utilidades — Copia de seguridad',
            'Copia de seguridad',
            'Herramientas para crear y restaurar respaldos de la base de datos.'
        );
    }

    public function crear(Request $request): void
    {
        $this->renderStub(
            $request,
            'Utilidades — Hacer copia',
            'Hacer copia de seguridad',
            'Generación de archivo de respaldo.'
        );
    }

    public function restaurar(Request $request): void
    {
        $this->renderStub(
            $request,
            'Utilidades — Restaurar copia',
            'Restaurar copia de seguridad',
            'Restauración desde archivo de respaldo.'
        );
    }
}
