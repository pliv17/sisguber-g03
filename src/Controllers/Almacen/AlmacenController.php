<?php

declare(strict_types=1);

namespace App\Controllers\Almacen;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;
use App\Core\Response;
use App\Services\StockService;
use App\Services\KardexService;

/**
 * Procesos de almacén: NEA, PECOSA, combustible, stock, kardex.
 */
final class AlmacenController
{
    use RendersStubPage;

    /**
     * Obtiene los años disponibles en el sistema desde 2012.
     *
     * @return array<int, string>
     */
    private function getAvailableYears(): array
    {
        try {
            $stockService = new StockService();
            return $stockService->getAvailableYears();
        } catch (\Exception $e) {
            // Fallback: años desde 2012 hasta hoy
            $years = [];
            $current = date('Y');
            for ($year = $current; $year >= 2012; $year--) {
                $years[] = (string) $year;
            }
            return $years;
        }
    }

    public function notasEntrada(Request $request): void
    {
        $filters = [
            'ano'         => $request->query('ano', date('Y')),
            'proveedor'   => $request->query('proveedor', ''),
            'nombre'      => $request->query('nombre', ''),
            'norden'      => $request->query('norden', ''),
            'estado'      => $request->query('estado', ''),
            'fechainicio' => $request->query('fechainicio', ''),
            'fechafin'    => $request->query('fechafin', ''),
        ];

        $legacyNeas = $this->getLegacyNeaSampleRows();
        $neaRows = $this->filterLegacyNeas($legacyNeas, $filters);

        Response::view('layouts.main', [
            'pageTitle'   => 'NEA — Nota de entrada al almacén',
            'contentView' => 'almacen/notas-entrada',
            'moduleTitle' => 'Nota de entrada al almacén (NEA)',
            'moduleLead'  => 'Consulta, registro y mantenimiento de notas de entrada del almacén.',
            'filters'     => $filters,
            'availableYears' => $this->getAvailableYears(),
            'moduleStats' => [
                ['label' => 'Origen legado', 'value' => 'notaentrada.php'],
                ['label' => 'Panel actual', 'value' => '/almacen/notas-entrada'],
                ['label' => 'Estado', 'value' => 'Consulta activa'],
                ['label' => 'Resultados', 'value' => (string) count($neaRows)],
            ],
            'moduleActions' => [
                [
                    'title' => 'Registrar NEA manual',
                    'description' => 'Pantalla de captura principal para construir la nota de entrada.',
                    'icon' => 'bi-journal-plus',
                    'color' => 'primary',
                ],
                [
                    'title' => 'Ingresar productos',
                    'description' => 'Flujo de detalle de bienes y cantidades asociados a la nota.',
                    'icon' => 'bi-box-seam',
                    'color' => 'success',
                ],
                [
                    'title' => 'Vincular proveedor',
                    'description' => 'Soporte para altas y mantenimiento de datos del proveedor.',
                    'icon' => 'bi-truck',
                    'color' => 'info',
                ],
                [
                    'title' => 'Afectación OC / factura',
                    'description' => 'Bloques para asociar orden de compra y comprobantes.',
                    'icon' => 'bi-receipt',
                    'color' => 'warning',
                ],
            ],
            'neaRows' => $neaRows,
            'classicBlocks' => [
                [
                    'title' => 'Pantalla principal',
                    'items' => ['notaentrada.php', 'guardar_orden.php', 'comprobarorden.php'],
                ],
                [
                    'title' => 'Ingreso y modificación',
                    'items' => [
                        'ingreso_afectacionoc.php',
                        'modifica_afectacionoc.php',
                        'ingreso_facturasoc.php',
                        'modifica_facturasoc.php',
                    ],
                ],
                [
                    'title' => 'Maestros auxiliares',
                    'items' => [
                        'autofuente.php',
                        'automedida.php',
                        'autometa.php',
                        'autooficina.php',
                        'autopartida.php',
                        'autorubro.php',
                    ],
                ],
                [
                    'title' => 'Consultas y mantenimiento',
                    'items' => [
                        'ver_articulos.php',
                        'ver_proveedores.php',
                        'eliminar_linea.php',
                        'anula_orden.php',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return array<int, array{norden:string, proveedor:string, total:float, fecha:string, estado:string, ano:string}>
     */
    private function getLegacyNeaSampleRows(): array
    {
        return [
            ['norden' => '00000125', 'proveedor' => 'SERVICIOS GENERALES DEL SUR S.A.C.', 'total' => 3185.40, 'fecha' => '12/04/2026', 'estado' => 'Normal', 'ano' => '2026'],
            ['norden' => '00000124', 'proveedor' => 'DISTRIBUIDORA PERUANA DE INSUMOS E.I.R.L.', 'total' => 1240.00, 'fecha' => '03/04/2026', 'estado' => 'Normal', 'ano' => '2026'],
            ['norden' => '00000119', 'proveedor' => 'CORPORACION ANDINA DE BIENES S.A.C.', 'total' => 892.15, 'fecha' => '18/03/2026', 'estado' => 'Anulada', 'ano' => '2026'],
            ['norden' => '00000102', 'proveedor' => 'COMERCIAL LA REINA S.A.', 'total' => 5590.75, 'fecha' => '22/12/2025', 'estado' => 'Normal', 'ano' => '2025'],
        ];
    }

    /**
     * @param array<string, string> $filters
     * @param array<int, array{norden:string, proveedor:string, total:float, fecha:string, estado:string, ano:string}> $rows
     * @return array<int, array{norden:string, proveedor:string, total:float, fecha:string, estado:string, ano:string}>
     */
    private function filterLegacyNeas(array $rows, array $filters): array
    {
        return array_values(array_filter($rows, static function (array $row) use ($filters): bool {
            if (($filters['ano'] ?? '') !== '' && $row['ano'] !== (string) $filters['ano']) {
                return false;
            }

            if (($filters['proveedor'] ?? '') !== '' && !str_contains(mb_strtolower($row['proveedor']), mb_strtolower((string) $filters['proveedor']))) {
                return false;
            }

            if (($filters['nombre'] ?? '') !== '' && !str_contains(mb_strtolower($row['proveedor']), mb_strtolower((string) $filters['nombre']))) {
                return false;
            }

            if (($filters['norden'] ?? '') !== '' && !str_contains($row['norden'], (string) $filters['norden'])) {
                return false;
            }

            if (($filters['estado'] ?? '') !== '' && $filters['estado'] !== '0') {
                $estadoFiltro = (string) $filters['estado'];
                $estadoEsperado = $estadoFiltro === '2' ? 'Anulada' : 'Normal';
                if ($row['estado'] !== $estadoEsperado) {
                    return false;
                }
            }

            return true;
        }));
    }

    public function pecosas(Request $request): void
    {
        $filters = [
            'ano'       => $request->query('ano', date('Y')),
            'almacen'   => $request->query('almacen', ''),
            'oficina'   => $request->query('oficina', ''),
            'norden'    => $request->query('norden', ''),
            'estado'    => $request->query('estado', ''),
            'fechaini'  => $request->query('fechaini', ''),
            'fechafin'  => $request->query('fechafin', ''),
        ];

        $pecosaRows = $this->filterLegacyPecosas($this->getLegacyPecosaSampleRows(), $filters);

        Response::view('layouts.main', [
            'pageTitle'   => 'PECOSA — Pedido comprobante de salida',
            'contentView' => 'almacen/pecosas',
            'moduleTitle' => 'Pedido comprobante de salida (PECOSA)',
            'moduleLead'  => 'Consulta, registro y mantenimiento de PECOSA de almacén.',
            'filters'     => $filters,
            'availableYears' => $this->getAvailableYears(),
            'moduleStats' => [
                ['label' => 'Origen', 'value' => 'pecosa.php'],
                ['label' => 'Panel actual', 'value' => '/almacen/pecosas'],
                ['label' => 'Estado', 'value' => 'Consulta activa'],
                ['label' => 'Resultados', 'value' => (string) count($pecosaRows)],
            ],
            'moduleActions' => [
                [
                    'title' => 'Nueva PECOSA',
                    'description' => 'Formulario para registrar un nuevo comprobante de salida.',
                    'icon' => 'bi-file-earmark-plus',
                    'color' => 'primary',
                ],
                [
                    'title' => 'Modificar PECOSA',
                    'description' => 'Edición de cabecera y detalle de un comprobante existente.',
                    'icon' => 'bi-pencil-square',
                    'color' => 'success',
                ],
                [
                    'title' => 'Validar orden',
                    'description' => 'Verificación del documento de referencia antes de guardar.',
                    'icon' => 'bi-patch-check',
                    'color' => 'info',
                ],
                [
                    'title' => 'Anular salida',
                    'description' => 'Cambio de estado y anulación del comprobante.',
                    'icon' => 'bi-x-circle',
                    'color' => 'warning',
                ],
            ],
            'pecosaRows' => $pecosaRows,
            'classicBlocks' => [
                [
                    'title' => 'Cabecera y detalle',
                    'items' => ['pecosa.php', 'nueva_pecosa.php', 'modifica_pecosa.php'],
                ],
                [
                    'title' => 'Validaciones',
                    'items' => ['comprobarorden.php', 'validaoc.php', 'modificar_cantidad.php'],
                ],
                [
                    'title' => 'Ayudas y consultas',
                    'items' => ['ver_almacen.php', 'ver_articulos.php', 'ayuda_oficinas.php'],
                ],
                [
                    'title' => 'Impresión y control',
                    'items' => ['frame_lineas.php', 'frame_lineas2.php', 'rejilla.php'],
                ],
            ],
        ]);
    }

    /**
     * @return array<int, array{norden:string, almacen:string, oficina:string, proveedor:string, total:float, fecha:string, estado:string, ano:string}>
     */
    private function getLegacyPecosaSampleRows(): array
    {
        return [
            ['norden' => '00000231', 'almacen' => '001', 'oficina' => 'LOGISTICA', 'proveedor' => 'SERVICIOS GENERALES DEL SUR S.A.C.', 'total' => 870.30, 'fecha' => '08/05/2026', 'estado' => 'Normal', 'ano' => '2026'],
            ['norden' => '00000229', 'almacen' => '003', 'oficina' => 'ABASTECIMIENTO', 'proveedor' => 'DISTRIBUIDORA PERUANA DE INSUMOS E.I.R.L.', 'total' => 2150.00, 'fecha' => '22/04/2026', 'estado' => 'Normal', 'ano' => '2026'],
            ['norden' => '00000217', 'almacen' => '002', 'oficina' => 'MANTENIMIENTO', 'proveedor' => 'CORPORACION ANDINA DE BIENES S.A.C.', 'total' => 640.50, 'fecha' => '12/03/2026', 'estado' => 'Anulada', 'ano' => '2026'],
            ['norden' => '00000198', 'almacen' => '001', 'oficina' => 'ADMINISTRACION', 'proveedor' => 'COMERCIAL LA REINA S.A.', 'total' => 4020.80, 'fecha' => '18/11/2025', 'estado' => 'Normal', 'ano' => '2025'],
        ];
    }

    /**
     * @param array<string, string> $filters
     * @param array<int, array{norden:string, almacen:string, oficina:string, proveedor:string, total:float, fecha:string, estado:string, ano:string}> $rows
     * @return array<int, array{norden:string, almacen:string, oficina:string, proveedor:string, total:float, fecha:string, estado:string, ano:string}>
     */
    private function filterLegacyPecosas(array $rows, array $filters): array
    {
        return array_values(array_filter($rows, static function (array $row) use ($filters): bool {
            if (($filters['ano'] ?? '') !== '' && $row['ano'] !== (string) $filters['ano']) {
                return false;
            }

            if (($filters['almacen'] ?? '') !== '' && !str_contains(mb_strtolower($row['almacen']), mb_strtolower((string) $filters['almacen']))) {
                return false;
            }

            if (($filters['oficina'] ?? '') !== '' && !str_contains(mb_strtolower($row['oficina']), mb_strtolower((string) $filters['oficina']))) {
                return false;
            }

            if (($filters['norden'] ?? '') !== '' && !str_contains($row['norden'], (string) $filters['norden'])) {
                return false;
            }

            if (($filters['estado'] ?? '') !== '' && $filters['estado'] !== '0') {
                $estadoEsperado = (string) $filters['estado'] === '2' ? 'Anulada' : 'Normal';
                if ($row['estado'] !== $estadoEsperado) {
                    return false;
                }
            }

            return true;
        }));
    }

    public function pecosasCombustible(Request $request): void
    {
        $filters = [
            'ano'       => $request->query('ano', date('Y')),
            'almacen'   => $request->query('almacen', ''),
            'oficina'   => $request->query('oficina', ''),
            'norden'    => $request->query('norden', ''),
            'estado'    => $request->query('estado', ''),
            'fechaini'  => $request->query('fechaini', ''),
            'fechafin'  => $request->query('fechafin', ''),
        ];

        $rows = $this->filterLegacyPecosaCombustible($this->getLegacyPecosaCombustibleSampleRows(), $filters);

        Response::view('layouts.main', [
            'pageTitle'   => 'PECOSA combustible',
            'contentView' => 'almacen/pecosas-combustible',
            'moduleTitle' => 'PECOSA combustible',
            'moduleLead'  => 'Consulta, registro y mantenimiento de PECOSA de combustible.',
            'filters'     => $filters,
            'availableYears' => $this->getAvailableYears(),
            'moduleStats' => [
                ['label' => 'Origen', 'value' => 'pecosa_combustible/pecosa.php'],
                ['label' => 'Panel actual', 'value' => '/almacen/pecosas-combustible'],
                ['label' => 'Estado', 'value' => 'Consulta activa'],
                ['label' => 'Resultados', 'value' => (string) count($rows)],
            ],
            'moduleActions' => [
                [
                    'title' => 'Nueva PECOSA combustible',
                    'description' => 'Registro de salidas de combustible con su detalle.',
                    'icon' => 'bi-fuel-pump',
                    'color' => 'primary',
                ],
                [
                    'title' => 'Modificar comprobante',
                    'description' => 'Ajuste de cabecera y líneas de una PECOSA existente.',
                    'icon' => 'bi-pencil-square',
                    'color' => 'success',
                ],
                [
                    'title' => 'Validar orden',
                    'description' => 'Verificación del documento de referencia antes de guardar.',
                    'icon' => 'bi-patch-check',
                    'color' => 'info',
                ],
                [
                    'title' => 'Anular salida',
                    'description' => 'Cambio de estado y anulación de la salida de combustible.',
                    'icon' => 'bi-x-circle',
                    'color' => 'warning',
                ],
            ],
            'pecosaCombustibleRows' => $rows,
            'classicBlocks' => [
                [
                    'title' => 'Cabecera y detalle',
                    'items' => ['pecosa.php', 'nueva_pecosa.php', 'modifica_pecosa.php'],
                ],
                [
                    'title' => 'Validaciones',
                    'items' => ['comprobarorden.php', 'validaoc.php', 'modificar_cantidad.php'],
                ],
                [
                    'title' => 'Ayudas y consultas',
                    'items' => ['ver_almacen.php', 'ver_articulos.php', 'ayuda_oficinas.php'],
                ],
                [
                    'title' => 'Documentos relacionados',
                    'items' => ['ver_facturas.php', 'ver_facturasMo.php', 'rejilla.php'],
                ],
            ],
        ]);
    }

    /**
     * @return array<int, array{norden:string, almacen:string, oficina:string, proveedor:string, combustible:string, cantidad:float, total:float, fecha:string, estado:string, ano:string}>
     */
    private function getLegacyPecosaCombustibleSampleRows(): array
    {
        return [
            ['norden' => '00000312', 'almacen' => '001', 'oficina' => 'TRANSPORTES', 'proveedor' => 'PETROPERU S.A.', 'combustible' => 'Diesel', 'cantidad' => 120.00, 'total' => 1620.00, 'fecha' => '10/05/2026', 'estado' => 'Normal', 'ano' => '2026'],
            ['norden' => '00000308', 'almacen' => '002', 'oficina' => 'OBRAS', 'proveedor' => 'GRIFO EL SOL S.A.C.', 'combustible' => 'Gasohol 90', 'cantidad' => 95.50, 'total' => 1047.30, 'fecha' => '02/05/2026', 'estado' => 'Normal', 'ano' => '2026'],
            ['norden' => '00000299', 'almacen' => '001', 'oficina' => 'LOGISTICA', 'proveedor' => 'SERVICENTRO NORTE E.I.R.L.', 'combustible' => 'Gasohol 95', 'cantidad' => 72.00, 'total' => 846.00, 'fecha' => '15/04/2026', 'estado' => 'Anulada', 'ano' => '2026'],
            ['norden' => '00000271', 'almacen' => '003', 'oficina' => 'MANTENIMIENTO', 'proveedor' => 'ESTACION DE SERVICIOS DEL SUR S.A.C.', 'combustible' => 'Diesel', 'cantidad' => 140.00, 'total' => 1795.00, 'fecha' => '08/12/2025', 'estado' => 'Normal', 'ano' => '2025'],
        ];
    }

    /**
     * @param array<string, string> $filters
     * @param array<int, array{norden:string, almacen:string, oficina:string, proveedor:string, combustible:string, cantidad:float, total:float, fecha:string, estado:string, ano:string}> $rows
     * @return array<int, array{norden:string, almacen:string, oficina:string, proveedor:string, combustible:string, cantidad:float, total:float, fecha:string, estado:string, ano:string}>
     */
    private function filterLegacyPecosaCombustible(array $rows, array $filters): array
    {
        return array_values(array_filter($rows, static function (array $row) use ($filters): bool {
            if (($filters['ano'] ?? '') !== '' && $row['ano'] !== (string) $filters['ano']) {
                return false;
            }

            if (($filters['almacen'] ?? '') !== '' && !str_contains(mb_strtolower($row['almacen']), mb_strtolower((string) $filters['almacen']))) {
                return false;
            }

            if (($filters['oficina'] ?? '') !== '' && !str_contains(mb_strtolower($row['oficina']), mb_strtolower((string) $filters['oficina']))) {
                return false;
            }

            if (($filters['norden'] ?? '') !== '' && !str_contains($row['norden'], (string) $filters['norden'])) {
                return false;
            }

            if (($filters['estado'] ?? '') !== '' && $filters['estado'] !== '0') {
                $estadoEsperado = (string) $filters['estado'] === '2' ? 'Anulada' : 'Normal';
                if ($row['estado'] !== $estadoEsperado) {
                    return false;
                }
            }

            return true;
        }));
    }

    public function valesCombustible(Request $request): void
    {
        $filters = [
            'ano'       => $request->query('ano', date('Y')),
            'vehiculo'  => $request->query('vehiculo', ''),
            'oficina'   => $request->query('oficina', ''),
            'nvale'     => $request->query('nvale', ''),
            'estado'    => $request->query('estado', ''),
            'fechaini'  => $request->query('fechaini', ''),
            'fechafin'  => $request->query('fechafin', ''),
        ];

        $rows = $this->filterLegacyValesCombustible($this->getLegacyValesCombustibleSampleRows(), $filters);

        Response::view('layouts.main', [
            'pageTitle'   => 'Vales de combustible',
            'contentView' => 'almacen/vales-combustible',
            'moduleTitle' => 'Vales de combustible',
            'moduleLead'  => 'Emisión y control de vales de combustible.',
            'filters'     => $filters,
            'availableYears' => $this->getAvailableYears(),
            'moduleStats' => [
                ['label' => 'Origen', 'value' => 'vales_combustible/vale.php'],
                ['label' => 'Panel actual', 'value' => '/almacen/vales-combustible'],
                ['label' => 'Estado', 'value' => 'Consulta activa'],
                ['label' => 'Resultados', 'value' => (string) count($rows)],
            ],
            'moduleActions' => [
                [
                    'title' => 'Nuevo vale',
                    'description' => 'Emisión de nuevo vale de combustible.',
                    'icon' => 'bi-file-earmark-plus',
                    'color' => 'primary',
                ],
                [
                    'title' => 'Modificar vale',
                    'description' => 'Edición de cabecera y líneas de un vale.',
                    'icon' => 'bi-pencil-square',
                    'color' => 'success',
                ],
                [
                    'title' => 'Imprimir vale',
                    'description' => 'Generar comprobante/impresión del vale.',
                    'icon' => 'bi-printer',
                    'color' => 'info',
                ],
                [
                    'title' => 'Anular vale',
                    'description' => 'Anulación y control del vale emitido.',
                    'icon' => 'bi-x-circle',
                    'color' => 'warning',
                ],
            ],
            'valesRows' => $rows,
            'classicBlocks' => [
                [
                    'title' => 'Cabecera y detalle',
                    'items' => ['nuevo_vale.php', 'modifica_vale.php', 'vale.php'],
                ],
                [
                    'title' => 'Operaciones',
                    'items' => ['guardar_orden.php', 'anula_orden.php', 'eliminar_lineas2.php'],
                ],
                [
                    'title' => 'Impresión y utilidades',
                    'items' => ['frame_lineas.php', 'frame_articulos.php', 'rejilla.php'],
                ],
            ],
        ]);
    }

    /**
     * @return array<int, array{nvale:string, vehiculo:string, oficina:string, combustible:string, litros:float, total:float, fecha:string, estado:string, ano:string}>
     */
    private function getLegacyValesCombustibleSampleRows(): array
    {
        return [
            ['nvale' => 'V-000125', 'vehiculo' => 'CAM-001', 'oficina' => 'TRANSPORTES', 'combustible' => 'Diesel', 'litros' => 120.00, 'total' => 540.00, 'fecha' => '11/05/2026', 'estado' => 'Normal', 'ano' => '2026'],
            ['nvale' => 'V-000122', 'vehiculo' => 'CAM-005', 'oficina' => 'OBRAS', 'combustible' => 'Gasohol 90', 'litros' => 85.50, 'total' => 382.95, 'fecha' => '29/04/2026', 'estado' => 'Normal', 'ano' => '2026'],
            ['nvale' => 'V-000118', 'vehiculo' => 'CAM-003', 'oficina' => 'MANTENIMIENTO', 'combustible' => 'Gasohol 95', 'litros' => 60.00, 'total' => 270.00, 'fecha' => '18/04/2026', 'estado' => 'Anulada', 'ano' => '2026'],
            ['nvale' => 'V-000099', 'vehiculo' => 'CAM-002', 'oficina' => 'LOGISTICA', 'combustible' => 'Diesel', 'litros' => 140.00, 'total' => 630.00, 'fecha' => '02/12/2025', 'estado' => 'Normal', 'ano' => '2025'],
        ];
    }

    /**
     * @param array<string, string> $filters
     * @param array<int, array{nvale:string, vehiculo:string, oficina:string, combustible:string, litros:float, total:float, fecha:string, estado:string, ano:string}> $rows
     * @return array<int, array{nvale:string, vehiculo:string, oficina:string, combustible:string, litros:float, total:float, fecha:string, estado:string, ano:string}>
     */
    private function filterLegacyValesCombustible(array $rows, array $filters): array
    {
        return array_values(array_filter($rows, static function (array $row) use ($filters): bool {
            if (($filters['ano'] ?? '') !== '' && $row['ano'] !== (string) $filters['ano']) {
                return false;
            }

            if (($filters['vehiculo'] ?? '') !== '' && !str_contains(mb_strtolower($row['vehiculo']), mb_strtolower((string) $filters['vehiculo']))) {
                return false;
            }

            if (($filters['oficina'] ?? '') !== '' && !str_contains(mb_strtolower($row['oficina']), mb_strtolower((string) $filters['oficina']))) {
                return false;
            }

            if (($filters['nvale'] ?? '') !== '' && !str_contains($row['nvale'], (string) $filters['nvale'])) {
                return false;
            }

            if (($filters['estado'] ?? '') !== '' && $filters['estado'] !== '0') {
                $estadoEsperado = (string) $filters['estado'] === '2' ? 'Anulada' : 'Normal';
                if ($row['estado'] !== $estadoEsperado) {
                    return false;
                }
            }

            return true;
        }));
    }

    public function stock(Request $request): void
    {
        $stockService = new StockService();

        // Use latest available year as default
        $defaultYear = $stockService->getLatestYear();
        
        $filters = [
            'ano'       => $request->query('ano', $defaultYear),
            'almacen'   => $request->query('almacen', ''),
            'articulo'  => $request->query('articulo', ''),
            'estado'    => $request->query('estado', ''),
        ];

        // Obtener datos reales de la BD
        $dbRows = $stockService->getStockRecords($filters);
        $stats = $stockService->getStockStats();
        $availableYears = $stockService->getAvailableYears();

        // Transformar datos para la vista
        $rows = array_map(static function (array $row): array {
            return [
                'articulo'       => $row['articulo'] ?? '',
                'descripcion'    => $row['desarticulo'] ?? '',
                'almacen'        => $row['almacen'] ?? '',
                'unidad'         => $row['medida'] ?? '',
                'ingresos'       => (float) ($row['cantidading'] ?? 0),
                'salidas'        => (float) ($row['cantidadsal'] ?? 0),
                'precio_promedio' => (float) ($row['preciopromedio'] ?? 0),
                'total'          => (float) ($row['importetotal'] ?? 0),
                'saldo_stock'    => (float) ($row['saldo'] ?? 0),
                'ano'            => $row['ano'] ?? '',
                'estado'         => $row['estado'] ?? '',
            ];
        }, $dbRows);

        Response::view('layouts.main', [
            'pageTitle'   => 'Stock de productos',
            'contentView' => 'almacen/stock',
            'moduleTitle' => 'Stock de productos',
            'moduleLead'  => 'Consulta y actualización de existencias en almacén.',
            'filters'     => $filters,
            'availableYears' => $availableYears,
            'moduleStats' => [
                ['label' => 'Total artículos', 'value' => (string) $stats['total_items']],
                ['label' => 'Valor stock', 'value' => '$' . number_format($stats['total_value'], 2)],
                ['label' => 'Almacenes', 'value' => (string) $stats['total_warehouses']],
                ['label' => 'Activos', 'value' => (string) $stats['active_items']],
            ],
            'moduleActions' => [
                [
                    'title' => 'Actualizar stock',
                    'description' => 'Ajuste de inventario físico por artículo.',
                    'icon' => 'bi-arrow-repeat',
                    'color' => 'primary',
                ],
                [
                    'title' => 'Ingreso de productos',
                    'description' => 'Recepción de nuevos artículos al almacén.',
                    'icon' => 'bi-box-seam',
                    'color' => 'success',
                ],
                [
                    'title' => 'Salida de productos',
                    'description' => 'Egreso de artículos del almacén.',
                    'icon' => 'bi-box-arrow-left',
                    'color' => 'info',
                ],
                [
                    'title' => 'Generar reporte',
                    'description' => 'Exportar reporte de stock a PDF.',
                    'icon' => 'bi-file-pdf',
                    'color' => 'warning',
                ],
            ],
            'stockRows' => $rows,
            'classicBlocks' => [
                [
                    'title' => 'Consultas y reportes',
                    'items' => ['reporteStocke.php', 'comunStock.php', 'reporteStocke.php'],
                ],
                [
                    'title' => 'Maestros y validaciones',
                    'items' => ['articulos.php', 'familias.php', 'ubicaciones.php'],
                ],
                [
                    'title' => 'Impresión',
                    'items' => ['imprimir_articulos_costo.php', 'imprimir_bajo_minimos.php', 'rejilla.php'],
                ],
            ],
        ]);
    }

    public function kardex(Request $request): void
    {
        $kardexService = new KardexService();

        // Use latest available year as default
        $defaultYear = $kardexService->getLatestYear();
        
        $filters = [
            'ano'       => $request->query('ano', $defaultYear),
            'almacen'   => $request->query('almacen', ''),
            'articulo'  => $request->query('articulo', ''),
            'tipodoc'   => $request->query('tipodoc', ''),
        ];

        // Obtener datos reales de la BD
        $dbRows = $kardexService->getKardexRecords($filters);
        $stats = $kardexService->getKardexStats();
        $availableYears = $kardexService->getAvailableYears();

        // Transformar datos para la vista
        $rows = array_map(static function (array $row): array {
            return [
                'fecha'           => $row['fecha'] ?? '',
                'tipodoc'         => $row['tipodoc'] ?? '',
                'destipodoc'      => $row['destipodoc'] ?? '',
                'numero'          => $row['numero'] ?? '',
                'almacen'         => $row['almacen'] ?? '',
                'articulo'        => $row['articulo'] ?? '',
                'descripcion'     => $row['desarticulo'] ?? '',
                'medida'          => $row['medida'] ?? '',
                'ingresos'        => (float) ($row['cantidading'] ?? 0),
                'salidas'         => (float) ($row['cantidadsal'] ?? 0),
                'saldo'           => (float) ($row['saldo'] ?? 0),
                'precio_promedio' => (float) ($row['preciopromedio'] ?? 0),
                'total'           => (float) ($row['importetotal'] ?? 0),
                'ano'             => $row['ano'] ?? '',
                'estado'          => $row['estado'] ?? '',
            ];
        }, $dbRows);

        Response::view('layouts.main', [
            'pageTitle'   => 'Kardex de productos',
            'contentView' => 'almacen/kardex',
            'moduleTitle' => 'Kardex de productos',
            'moduleLead'  => 'Movimientos valorizados por producto.',
            'filters'     => $filters,
            'availableYears' => $availableYears,
            'moduleStats' => [
                ['label' => 'Movimientos', 'value' => (string) $stats['total_movements']],
                ['label' => 'Artículos', 'value' => (string) $stats['total_articles']],
                ['label' => 'Almacenes', 'value' => (string) $stats['total_warehouses']],
                ['label' => 'Valor total', 'value' => '$' . number_format($stats['total_value'], 2)],
            ],
            'moduleActions' => [
                [
                    'title' => 'Consultar movimientos',
                    'description' => 'Búsqueda de movimientos por período.',
                    'icon' => 'bi-search',
                    'color' => 'primary',
                ],
                [
                    'title' => 'Generar reporte',
                    'description' => 'Exportar reporte de kardex a PDF.',
                    'icon' => 'bi-file-pdf',
                    'color' => 'warning',
                ],
                [
                    'title' => 'Análisis de stock',
                    'description' => 'Comparar con saldo de stock.',
                    'icon' => 'bi-bar-chart',
                    'color' => 'info',
                ],
            ],
            'kardexRows' => $rows,
            'classicBlocks' => [
                [
                    'title' => 'Consultas y reportes',
                    'items' => ['komunStock.php', 'ReporteKardex.php'],
                ],
                [
                    'title' => 'Análisis',
                    'items' => ['reporteStocke.php', 'imprimir_articulos_costo.php'],
                ],
            ],
        ]);
    }
}
