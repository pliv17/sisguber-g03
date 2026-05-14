<?php

declare(strict_types=1);

namespace App\Controllers\Maestros;

use App\Core\Request;
use App\Core\Response;

/**
 * UI Maestros — listados con Ajax (configuración por recurso).
 */
final class MaestrosController
{
    /** @var array<string, array<string, mixed>> */
    private const PAGES = [
        'almacenes' => [
            'title'   => 'Maestros — Almacenes',
            'heading' => 'Almacenes',
            'api'     => '/api/maestros/almacenes',
            'pk'      => 'code',
            'year'    => false,
            'columns' => [
                ['key' => 'code', 'label' => 'Código'],
                ['key' => 'name', 'label' => 'Nombre'],
                ['key' => 'address', 'label' => 'Dirección'],
            ],
            'fields' => [
                ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'required' => true],
                ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
                ['name' => 'address', 'label' => 'Dirección', 'type' => 'text', 'required' => false],
            ],
        ],
        'unidades-medida' => [
            'title'   => 'Maestros — Unidades de medida',
            'heading' => 'Unidades de medida',
            'api'     => '/api/maestros/unidades-medida',
            'pk'      => 'code',
            'year'    => false,
            'columns' => [
                ['key' => 'code', 'label' => 'Código'],
                ['key' => 'name', 'label' => 'Nombre'],
            ],
            'fields' => [
                ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'required' => true],
                ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
            ],
        ],
        'rubros-proveedor' => [
            'title'   => 'Maestros — Rubros de proveedor',
            'heading' => 'Rubros de proveedor',
            'api'     => '/api/maestros/rubros-proveedor',
            'pk'      => 'code',
            'year'    => false,
            'columns' => [
                ['key' => 'code', 'label' => 'Código'],
                ['key' => 'description', 'label' => 'Descripción'],
            ],
            'fields' => [
                ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'required' => true],
                ['name' => 'description', 'label' => 'Descripción', 'type' => 'text', 'required' => true],
            ],
        ],
        'proveedores' => [
            'title'   => 'Maestros — Proveedores',
            'heading' => 'Proveedores',
            'api'     => '/api/maestros/proveedores',
            'pk'      => 'ruc',
            'year'    => false,
            'columns' => [
                ['key' => 'ruc', 'label' => 'RUC'],
                ['key' => 'name', 'label' => 'Nombre'],
                ['key' => 'address', 'label' => 'Dirección'],
            ],
            'fields' => [
                ['name' => 'ruc', 'label' => 'RUC (11 dígitos)', 'type' => 'text', 'required' => true, 'maxlength' => 11],
                ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
                ['name' => 'address', 'label' => 'Dirección', 'type' => 'text', 'required' => false],
            ],
        ],
        'catalogo-bienes' => [
            'title'   => 'Maestros — Catálogo de bienes',
            'heading' => 'Catálogo de bienes',
            'api'     => '/api/maestros/productos',
            'pk'      => 'code',
            'year'    => false,
            'columns' => [
                ['key' => 'code', 'label' => 'Código'],
                ['key' => 'name', 'label' => 'Nombre'],
                ['key' => 'measure_unit_code', 'label' => 'Und. medida'],
            ],
            'fields' => [
                ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'required' => true],
                ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
                ['name' => 'measure_unit_id', 'label' => 'ID unidad de medida', 'type' => 'number', 'required' => true],
            ],
        ],
        'catalogo-servicios' => [
            'title'   => 'Maestros — Catálogo de servicios',
            'heading' => 'Catálogo de servicios',
            'api'     => '/api/maestros/servicios',
            'pk'      => 'code',
            'year'    => false,
            'columns' => [
                ['key' => 'code', 'label' => 'Código'],
                ['key' => 'name', 'label' => 'Nombre'],
                ['key' => 'measure_unit_code', 'label' => 'Und. medida'],
            ],
            'fields' => [
                ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'required' => true],
                ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
                ['name' => 'measure_unit_id', 'label' => 'ID unidad de medida', 'type' => 'number', 'required' => true],
            ],
        ],
        'oficinas' => [
            'title'   => 'Maestros — Oficinas',
            'heading' => 'Oficinas',
            'api'     => '/api/maestros/oficinas',
            'pk'      => 'code',
            'year'    => false,
            'columns' => [
                ['key' => 'code', 'label' => 'Código'],
                ['key' => 'name', 'label' => 'Nombre'],
                ['key' => 'responsible', 'label' => 'Responsable'],
            ],
            'fields' => [
                ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'required' => true],
                ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
                ['name' => 'responsible', 'label' => 'Responsable', 'type' => 'text', 'required' => false],
            ],
        ],
        'fuentes-financiamiento' => [
            'title'   => 'Maestros — Fuentes de financiamiento',
            'heading' => 'Fuentes de financiamiento',
            'api'     => '/api/maestros/fuentes-financiamiento',
            'pk'      => 'code',
            'year'    => true,
            'columns' => [
                ['key' => 'year', 'label' => 'Año'],
                ['key' => 'code', 'label' => 'Código'],
                ['key' => 'name', 'label' => 'Nombre'],
            ],
            'fields' => [
                ['name' => 'year', 'label' => 'Año', 'type' => 'number', 'required' => true],
                ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'required' => true],
                ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
            ],
        ],
        'metas' => [
            'title'   => 'Maestros — Metas presupuestales',
            'heading' => 'Metas presupuestales',
            'api'     => '/api/maestros/metas',
            'pk'      => 'code',
            'year'    => true,
            'columns' => [
                ['key' => 'year', 'label' => 'Año'],
                ['key' => 'name', 'label' => 'Nombre'],
            ],
            'fields' => [
                ['name' => 'year', 'label' => 'Año', 'type' => 'number', 'required' => true],
                ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
                ['name' => 'description', 'label' => 'Descripción', 'type' => 'text', 'required' => false],
            ],
        ],
        'partidas' => [
            'title'   => 'Maestros — Partidas presupuestales',
            'heading' => 'Partidas presupuestales',
            'api'     => '/api/maestros/partidas',
            'pk'      => 'code',
            'year'    => true,
            'columns' => [
                ['key' => 'year', 'label' => 'Año'],
                ['key' => 'code', 'label' => 'Código'],
                ['key' => 'name', 'label' => 'Nombre'],
            ],
            'fields' => [
                ['name' => 'year', 'label' => 'Año', 'type' => 'number', 'required' => true],
                ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'required' => true],
                ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
            ],
        ],
    ];

    public function almacenes(Request $request): void
    {
        $this->render('almacenes');
    }

    public function unidadesMedida(Request $request): void
    {
        $this->render('unidades-medida');
    }

    public function rubrosProveedor(Request $request): void
    {
        $this->render('rubros-proveedor');
    }

    public function proveedores(Request $request): void
    {
        $this->render('proveedores');
    }

    public function catalogoBienes(Request $request): void
    {
        $this->render('catalogo-bienes');
    }

    public function catalogoServicios(Request $request): void
    {
        $this->render('catalogo-servicios');
    }

    public function oficinas(Request $request): void
    {
        $this->render('oficinas');
    }

    public function fuentesFinanciamiento(Request $request): void
    {
        $this->render('fuentes-financiamiento');
    }

    public function metas(Request $request): void
    {
        $this->render('metas');
    }

    public function partidas(Request $request): void
    {
        $this->render('partidas');
    }

    private function render(string $key): void
    {
        $cfg = self::PAGES[$key] ?? null;
        if ($cfg === null) {
            throw new \InvalidArgumentException('Página maestro desconocida: ' . $key);
        }
        Response::view('layouts.main', [
            'pageTitle'   => $cfg['title'],
            'contentView' => 'maestros/list',
            'maestroPage' => $key,
            'maestroList' => $cfg,
        ]);
    }
}
