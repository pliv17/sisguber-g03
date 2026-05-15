<?php

declare(strict_types=1);

namespace App\Controllers\Reportes;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;
use App\Core\Database;
use App\Core\Response;

final class ReportesLogisticaController
{
    use RendersStubPage;

    public function ordenesCompra(Request $request): void
    {
        $filters = [
            'ano' => $request->query('ano', date('Y')),
            'fechainicio' => $request->query('fechainicio', ''),
            'fechafin' => $request->query('fechafin', ''),
            'proveedor' => $request->query('proveedor', ''),
            'norden' => $request->query('norden', ''),
            'estado' => $request->query('estado', ''),
        ];

        $pdo = Database::getInstance()->getConnection();

        $where = ' WHERE 1=1 ';
        $params = [];

        if ($filters['ano'] !== '') {
            $where .= ' AND movicompras.ano_ordencom = :ano ';
            $params[':ano'] = $filters['ano'];
        }

        if ($filters['proveedor'] !== '') {
            $where .= ' AND movicompras.proveedor_ordencom = :proveedor ';
            $params[':proveedor'] = $filters['proveedor'];
        }

        if ($filters['norden'] !== '') {
            $where .= ' AND movicompras.norden = :norden ';
            $params[':norden'] = $filters['norden'];
        }

        if ($filters['estado'] !== '') {
            $where .= ' AND movicompras.estado_ordencom = :estado ';
            $params[':estado'] = $filters['estado'];
        }

        if ($filters['fechainicio'] !== '' && $filters['fechafin'] !== '') {
            $where .= " AND (STR_TO_DATE(movicompras.fecha_orden, '%d/%m/%Y') >= STR_TO_DATE(:fi, '%d/%m/%Y') AND STR_TO_DATE(movicompras.fecha_orden, '%d/%m/%Y') <= STR_TO_DATE(:ff, '%d/%m/%Y')) ";
            $params[':fi'] = $filters['fechainicio'];
            $params[':ff'] = $filters['fechafin'];
        }

        $sql = 'SELECT movicompras.norden, movicompras.ano_ordencom as ano, movicompras.fecha_orden as fecha, movicompras.total_ordencom as total, movicompras.estado_ordencom as estado, proveedores.nombre_provee as proveedor'
            . ' FROM movicompras '
            . ' JOIN proveedores ON movicompras.proveedor_ordencom = proveedores.ruc_provee '
            . $where
            . ' ORDER BY movicompras.norden DESC ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        Response::view('layouts.main', [
            'pageTitle' => 'Reporte — Órdenes de compra',
            'contentView' => 'reportes/logistica/ordenes-compra',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }

    public function ordenesServicio(Request $request): void
    {
        $filters = [
            'ano' => $request->query('ano', date('Y')),
            'fechainicio' => $request->query('fechainicio', ''),
            'fechafin' => $request->query('fechafin', ''),
            'proveedor' => $request->query('proveedor', ''),
            'norden' => $request->query('norden', ''),
            'estado' => $request->query('estado', ''),
        ];

        $pdo = Database::getInstance()->getConnection();

        $where = ' WHERE 1=1 ';
        $params = [];

        if ($filters['ano'] !== '') {
            $where .= ' AND moviservicios.ano_ordencom = :ano ';
            $params[':ano'] = $filters['ano'];
        }

        if ($filters['proveedor'] !== '') {
            $where .= ' AND moviservicios.proveedor_ordencom = :proveedor ';
            $params[':proveedor'] = $filters['proveedor'];
        }

        if ($filters['norden'] !== '') {
            $where .= ' AND moviservicios.norden = :norden ';
            $params[':norden'] = $filters['norden'];
        }

        if ($filters['estado'] !== '') {
            $where .= ' AND moviservicios.estado_ordencom = :estado ';
            $params[':estado'] = $filters['estado'];
        }

        if ($filters['fechainicio'] !== '' && $filters['fechafin'] !== '') {
            $where .= " AND (STR_TO_DATE(moviservicios.fecha_orden, '%d/%m/%Y') >= STR_TO_DATE(:fi, '%d/%m/%Y') AND STR_TO_DATE(moviservicios.fecha_orden, '%d/%m/%Y') <= STR_TO_DATE(:ff, '%d/%m/%Y')) ";
            $params[':fi'] = $filters['fechainicio'];
            $params[':ff'] = $filters['fechafin'];
        }

        $sql = 'SELECT moviservicios.norden, moviservicios.ano_ordencom as ano, moviservicios.fecha_orden as fecha, moviservicios.total_ordencom as total, moviservicios.estado_ordencom as estado, proveedores.nombre_provee as proveedor'
            . ' FROM moviservicios '
            . ' JOIN proveedores ON moviservicios.proveedor_ordencom = proveedores.ruc_provee '
            . $where
            . ' ORDER BY moviservicios.norden DESC ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        Response::view('layouts.main', [
            'pageTitle' => 'Reporte — Órdenes de servicio',
            'contentView' => 'reportes/logistica/ordenes-servicio',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }
}
