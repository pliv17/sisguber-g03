<?php

declare(strict_types=1);

namespace App\Controllers\Reportes;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

final class ReportesAlmacenController
{
    use RendersStubPage;

    public function notasEntrada(Request $request): void
    {
        $this->renderStub(
            $request,
            'Reporte — NEA',
            'Reporte de notas de entrada',
            'NEA emitidas en el periodo.'
        );
    }

    public function pecosas(Request $request): void
    {
        $this->renderStub(
            $request,
            'Reporte — PECOSA',
            'Reporte de PECOSA',
            'Comprobantes de salida emitidos.'
        );
    }

    public function pecosasCombustible(Request $request): void
    {
        $this->renderStub(
            $request,
            'Reporte — PECOSA combustible',
            'Reporte de PECOSA combustible',
            'Salidas de combustible.'
        );
    }

    public function stockFisico(Request $request): void
    {
        $this->renderStub(
            $request,
            'Reporte — Stock físico',
            'Listado de stock físico por producto',
            'Existencias por producto y almacén.'
        );
    }

    public function kardex(Request $request): void
    {
        $this->renderStub(
            $request,
            'Reporte — Kardex',
            'Kardex diario por producto',
            'Movimientos diarios valorizados.'
        );
    }
}
