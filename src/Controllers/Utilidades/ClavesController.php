<?php

declare(strict_types=1);

namespace App\Controllers\Utilidades;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

final class ClavesController
{
    use RendersStubPage;

    public function index(Request $request): void
    {
        $this->renderStub(
            $request,
            'Utilidades — Mantenimiento de claves',
            'Mantenimiento de claves',
            'Cambio de contraseñas y parámetros de acceso.'
        );
    }
}
