<?php

declare(strict_types=1);

namespace App\Controllers\Utilidades;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

final class NeasManualesController
{
    use RendersStubPage;

    public function index(Request $request): void
    {
        $this->renderStub(
            $request,
            'Utilidades — NEA manuales',
            'Ingreso de NEA en forma manual',
            'Registro manual de notas de entrada.'
        );
    }
}
