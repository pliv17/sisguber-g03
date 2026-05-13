<?php

declare(strict_types=1);

namespace App\Controllers\Utilidades;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

final class PecosasManualesController
{
    use RendersStubPage;

    public function index(Request $request): void
    {
        $this->renderStub(
            $request,
            'Utilidades — PECOSA manuales',
            'Ingreso de PECOSA en forma manual',
            'Registro manual de comprobantes de salida.'
        );
    }
}
