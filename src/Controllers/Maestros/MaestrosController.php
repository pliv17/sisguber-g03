<?php

declare(strict_types=1);

namespace App\Controllers\Maestros;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;

/**
 * Archivos maestros — pantallas CRUD (stubs hasta implementar servicios).
 */
final class MaestrosController
{
    use RendersStubPage;

    public function almacenes(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Almacenes', 'Códigos de almacén', 'Gestión de almacenes y ubicaciones.');
    }

    public function unidadesMedida(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Unidades de medida', 'Unidades de medida', 'UM para bienes y servicios.');
    }

    public function rubrosProveedor(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Rubro del proveedor', 'Rubro del proveedor', 'Clasificación de proveedores.');
    }

    public function proveedores(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Proveedores', 'Proveedores de bienes y servicios', 'Registro y mantenimiento de proveedores.');
    }

    public function catalogoBienes(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Catálogo de bienes', 'Catálogo de bienes', 'Códigos y descripción de bienes.');
    }

    public function catalogoServicios(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Catálogo de servicios', 'Catálogo de servicios', 'Códigos y descripción de servicios.');
    }

    public function oficinas(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Oficinas', 'Códigos de oficinas', 'Estructura orgánica / oficinas.');
    }

    public function fuentesFinanciamiento(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Fuentes de financiamiento', 'Fuente de financiamiento / rubros', 'Fuentes y rubros presupuestales.');
    }

    public function metas(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Metas presupuestales', 'Metas presupuestales', 'Metas del ejercicio.');
    }

    public function partidas(Request $request): void
    {
        $this->renderStub($request, 'Maestros — Partidas presupuestales', 'Partidas presupuestales', 'Clasificador de gastos.');
    }
}
