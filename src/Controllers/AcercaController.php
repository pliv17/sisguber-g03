<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

final class AcercaController
{
    use RendersStubPage;

    public function creditos(Request $request): void
    {
        $this->renderStub(
            $request,
            'Acerca de — Créditos',
            'Créditos del programa',
            'Sistema de abastecimiento y almacén.'
        );
    }
}
