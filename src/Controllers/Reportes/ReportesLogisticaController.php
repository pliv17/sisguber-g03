<?php

declare(strict_types=1);

namespace App\Controllers\Reportes;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

final class ReportesLogisticaController
{
    use RendersStubPage;

    public function ordenesCompra(Request $request): void
    {
        $this->renderStub(
            $request,
            'Reporte — Órdenes de compra',
            'Listado de órdenes de compra',
            'Impresión o exportación del listado de órdenes de compra.'
        );
    }

    public function ordenesServicio(Request $request): void
    {
        $this->renderStub(
            $request,
            'Reporte — Órdenes de servicio',
            'Listado de órdenes de servicio',
            'Impresión o exportación del listado de órdenes de servicio.'
        );
    }
}
