<?php

declare(strict_types=1);

namespace App\Controllers\Logistica;

use App\Core\Request;
use App\Core\Response;

/**
 * LogisticaController — UI de Logística.
 *
 * Patrón idéntico al de MaestrosController:
 * - Un array PAGES define configuración por pantalla.
 * - Una vista genérica (logistica/list) recibe la config via PHP y la
 *   pasa al JS como data-config, igual que maestros/list.
 * - Cuadro Comparativo y Cuadro de Necesidades tienen vistas propias
 *   porque su layout es diferente a una lista simple.
 */
final class LogisticaController
{
    /** @var array<string, array<string, mixed>> */
    private const PAGES = [
        'orden-compra' => [
            'title'       => 'Logística — Órdenes de compra',
            'heading'     => 'Órdenes de compra',
            'badge'       => 'OC',
            'badge_color' => 'primary',
            'api'         => '/api/logistica/ordenes-compra',
            // Columnas que muestra la tabla genérica
            'columns' => [
                ['key' => 'norden',    'label' => 'N° Orden',  'mono' => true],
                ['key' => 'proveedor', 'label' => 'Proveedor'],
                ['key' => 'fecha',     'label' => 'Fecha',     'center' => true],
                ['key' => 'total',     'label' => 'Total (S/)', 'money' => true, 'right' => true],
                ['key' => 'estado',    'label' => 'Estado',    'badge' => true,  'center' => true],
            ],
            // Filtros que renderiza la vista genérica
            'filters' => [
                ['name' => 'year',      'label' => 'Periodo',         'type' => 'year'],
                ['name' => 'ruc',       'label' => 'RUC Proveedor',   'type' => 'text',   'mono' => true, 'maxlength' => 11],
                ['name' => 'q',         'label' => 'Nombre / R. Social','type' => 'text'],
                ['name' => 'norden',    'label' => 'N° Orden',        'type' => 'text',   'mono' => true],
                ['name' => 'estado',    'label' => 'Estado',          'type' => 'select',
                 'options' => [['v' => '0', 'l' => 'Todos'], ['v' => '1', 'l' => 'Normal'], ['v' => '2', 'l' => 'Anulada']]],
                ['name' => 'fecha_ini', 'label' => 'Fecha inicio',    'type' => 'date'],
                ['name' => 'fecha_fin', 'label' => 'Fecha fin',       'type' => 'date'],
            ],
            // Acciones de fila
            'row_actions' => ['cancel'],
            'can_create'  => false,   // el alta es un flujo complejo (otra pantalla)
        ],

        'orden-servicio' => [
            'title'       => 'Logística — Órdenes de servicio',
            'heading'     => 'Órdenes de servicio',
            'badge'       => 'OS',
            'badge_color' => 'info',
            'api'         => '/api/logistica/ordenes-servicio',
            'columns' => [
                ['key' => 'norden',    'label' => 'N° Orden',   'mono' => true],
                ['key' => 'proveedor', 'label' => 'Proveedor / Contratista'],
                ['key' => 'fecha',     'label' => 'Fecha',      'center' => true],
                ['key' => 'total',     'label' => 'Total (S/)', 'money' => true, 'right' => true],
                ['key' => 'estado',    'label' => 'Estado',     'badge' => true, 'center' => true],
            ],
            'filters' => [
                ['name' => 'year',      'label' => 'Periodo',       'type' => 'year'],
                ['name' => 'ruc',       'label' => 'RUC Proveedor', 'type' => 'text', 'mono' => true, 'maxlength' => 11],
                ['name' => 'q',         'label' => 'Nombre',        'type' => 'text'],
                ['name' => 'norden',    'label' => 'N° Orden',      'type' => 'text', 'mono' => true],
                ['name' => 'estado',    'label' => 'Estado',        'type' => 'select',
                 'options' => [['v' => '0', 'l' => 'Todos'], ['v' => '1', 'l' => 'Normal'], ['v' => '2', 'l' => 'Anulada']]],
                ['name' => 'fecha_ini', 'label' => 'Fecha inicio',  'type' => 'date'],
                ['name' => 'fecha_fin', 'label' => 'Fecha fin',     'type' => 'date'],
            ],
            'row_actions' => ['cancel'],
            'can_create'  => false,
        ],
    ];

    public function ordenCompra(Request $request): void
    {
        $this->render('orden-compra');
    }

    public function ordenServicio(Request $request): void
    {
        $this->render('orden-servicio');
    }

    /**
     * Cuadro comparativo — vista propia (layout distinto a lista simple).
     */
    public function cuadroComparativo(Request $request): void
    {
        Response::view('layouts.main', [
            'pageTitle'      => 'Logística — Cuadro comparativo',
            'contentView'    => 'logistica/cuadro_comparativo',
            'logisticaPage'  => 'cuadro-comparativo',
        ]);
    }

    /**
     * Cuadro de necesidades — vista propia con tabs.
     */
    public function cuadroNecesidades(Request $request): void
    {
        Response::view('layouts.main', [
            'pageTitle'      => 'Logística — Cuadro de necesidades',
            'contentView'    => 'logistica/cuadro_necesidades',
            'logisticaPage'  => 'cuadro-necesidades',
        ]);
    }

    // ── Privado ──────────────────────────────────────────────────

    private function render(string $key): void
    {
        $cfg = self::PAGES[$key] ?? null;
        if ($cfg === null) {
            throw new \InvalidArgumentException("Página logística desconocida: {$key}");
        }

        Response::view('layouts.main', [
            'pageTitle'      => $cfg['title'],
            'contentView'    => 'logistica/list',
            'logisticaPage'  => $key,
            'logisticaList'  => $cfg,
        ]);
    }
}
