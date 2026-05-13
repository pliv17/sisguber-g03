<?php

declare(strict_types=1);

namespace App\Controllers\Almacen;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

/**
 * Procesos de almacén: NEA, PECOSA, combustible, stock, kardex.
 */
final class AlmacenController
{
    use RendersStubPage;

    public function notasEntrada(Request $request): void
    {
        $this->renderStub(
            $request,
            'NEA — Nota de entrada al almacén',
            'Nota de entrada al almacén (NEA)',
            'Registro de ingresos a almacén.'
        );
    }

    public function pecosas(Request $request): void
    {
        $this->renderStub(
            $request,
            'PECOSA',
            'Pedido — comprobante de salida (PECOSA)',
            'Salidas de almacén estándar.'
        );
    }

    public function pecosasCombustible(Request $request): void
    {
        $this->renderStub(
            $request,
            'PECOSA combustible',
            'PECOSA combustible',
            'Salidas de combustible.'
        );
    }

    public function valesCombustible(Request $request): void
    {
        $this->renderStub(
            $request,
            'Vales de combustible',
            'Vales de combustible',
            'Emisión y control de vales.'
        );
    }

    public function stock(Request $request): void
    {
        $this->renderStub(
            $request,
            'Stock de productos',
            'Actualiza stock de productos',
            'Proceso de actualización de existencias.'
        );
    }

    public function kardex(Request $request): void
    {
        $this->renderStub(
            $request,
            'Kardex de productos',
            'Actualiza kardex de productos',
            'Movimientos valorizados por producto.'
        );
    }
}
