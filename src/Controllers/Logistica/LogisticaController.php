<?php

declare(strict_types=1);

namespace App\Controllers\Logistica;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

/**
 * Procesos de logística (compras, servicios, cuadros).
 */
final class LogisticaController
{
    use RendersStubPage;

    public function ordenCompra(Request $request): void
    {
        $this->renderStub(
            $request,
            'Órdenes de compra',
            'Genera y consulta orden de compra',
            'Emisión y seguimiento de órdenes de compra.'
        );
    }

    public function ordenServicio(Request $request): void
    {
        $this->renderStub(
            $request,
            'Órdenes de servicio',
            'Genera y consulta orden de servicio',
            'Emisión y seguimiento de órdenes de servicio.'
        );
    }

    public function cuadroComparativo(Request $request): void
    {
        $this->renderStub(
            $request,
            'Cuadro comparativo',
            'Genera y consulta cuadro comparativo',
            'Comparación de ofertas y documentos asociados.'
        );
    }

    public function cuadroNecesidades(Request $request): void
    {
        $this->renderStub(
            $request,
            'Cuadro de necesidades',
            'Registra cuadro de necesidades',
            'Registro del cuadro de necesidades de bienes/servicios.'
        );
    }
}
