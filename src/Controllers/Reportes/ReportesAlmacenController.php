<?php

declare(strict_types=1);

namespace App\Controllers\Reportes;

use App\Controllers\Concerns\RendersStubPage;
use App\Core\Request;
use App\Core\Database;
use App\Core\Response;

final class ReportesAlmacenController
{
    use RendersStubPage;

    public function notasEntrada(Request $request): void
    {
        $filters = [
            'ano' => $request->query('ano', date('Y')),
            'fechainicio' => $request->query('fechainicio', ''),
            'fechafin' => $request->query('fechafin', ''),
            'proveedor' => $request->query('proveedor', ''),
            'almacen' => $request->query('almacen', ''),
        ];

        $pdo = Database::getInstance()->getConnection();

        $where = ' WHERE 1=1 ';
        $params = [];

        if ($filters['ano'] !== '') {
            $where .= ' AND movineas.ano_ordencom = :ano ';
            $params[':ano'] = $filters['ano'];
        }

        if ($filters['proveedor'] !== '') {
            $where .= ' AND movineas.proveedor_ordencom = :proveedor ';
            $params[':proveedor'] = $filters['proveedor'];
        }

        if ($filters['almacen'] !== '') {
            $almacenCode = substr($filters['almacen'], 0, 3);
            $where .= ' AND movineas.almacen_ordencom = :almacen ';
            $params[':almacen'] = $almacenCode;
        }

        if ($filters['fechainicio'] !== '' && $filters['fechafin'] !== '') {
            $where .= " AND (STR_TO_DATE(movineas.fecha_orden, '%d/%m/%Y') >= STR_TO_DATE(:fi, '%d/%m/%Y') AND STR_TO_DATE(movineas.fecha_orden, '%d/%m/%Y') <= STR_TO_DATE(:ff, '%d/%m/%Y')) ";
            $params[':fi'] = $filters['fechainicio'];
            $params[':ff'] = $filters['fechafin'];
        }

        $sql = 'SELECT movineas.norden, movineas.ano_ordencom as ano, movineas.fecha_orden as fecha, movineas.total_ordencom as total, movineas.estado_ordencom as estado, movineas.almacen_ordencom as almacen, proveedores.nombre_provee as proveedor'
            . ' FROM movineas '
            . ' JOIN proveedores ON movineas.proveedor_ordencom = proveedores.ruc_provee '
            . $where
            . ' ORDER BY movineas.fecha_orden DESC, movineas.norden DESC ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        Response::view('layouts.main', [
            'pageTitle' => 'Reporte — NEA',
            'contentView' => 'reportes/almacen/notas-entrada',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }

    public function pecosas(Request $request): void
    {
        $filters = [
            'ano' => $request->query('ano', date('Y')),
            'fechainicio' => $request->query('fechainicio', ''),
            'fechafin' => $request->query('fechafin', ''),
            'oficina' => $request->query('oficina', ''),
            'almacen' => $request->query('almacen', ''),
        ];

        $pdo = Database::getInstance()->getConnection();

        $where = ' WHERE 1=1 ';
        $params = [];

        if ($filters['ano'] !== '') {
            $where .= ' AND movipecosa.ano_ordencom = :ano ';
            $params[':ano'] = $filters['ano'];
        }

        if ($filters['oficina'] !== '') {
            $where .= ' AND movipecosa.oficina_ordencom = :oficina ';
            $params[':oficina'] = substr($filters['oficina'], 0, 3);
        }

        if ($filters['almacen'] !== '') {
            $where .= ' AND movipecosa.almacen_ordencom = :almacen ';
            $params[':almacen'] = substr($filters['almacen'], 0, 3);
        }

        if ($filters['fechainicio'] !== '' && $filters['fechafin'] !== '') {
            $where .= " AND (STR_TO_DATE(movipecosa.fecha_orden, '%d/%m/%Y') >= STR_TO_DATE(:fi, '%d/%m/%Y') AND STR_TO_DATE(movipecosa.fecha_orden, '%d/%m/%Y') <= STR_TO_DATE(:ff, '%d/%m/%Y')) ";
            $params[':fi'] = $filters['fechainicio'];
            $params[':ff'] = $filters['fechafin'];
        }

        $sql = 'SELECT movipecosa.norden, movipecosa.ano_ordencom as ano, movipecosa.fecha_orden as fecha, movipecosa.total_ordencom as total, movipecosa.estado_ordencom as estado'
            . ' FROM movipecosa '
            . $where
            . ' ORDER BY movipecosa.fecha_orden DESC, movipecosa.norden DESC ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        Response::view('layouts.main', [
            'pageTitle' => 'Reporte — PECOSA',
            'contentView' => 'reportes/almacen/pecosas',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }

    public function pecosasCombustible(Request $request): void
    {
        $filters = [
            'ano' => $request->query('ano', date('Y')),
            'fechainicio' => $request->query('fechainicio', ''),
            'fechafin' => $request->query('fechafin', ''),
            'oficina' => $request->query('oficina', ''),
            'almacen' => $request->query('almacen', ''),
        ];

        $pdo = Database::getInstance()->getConnection();

        $where = ' WHERE 1=1 ';
        $params = [];

        if ($filters['ano'] !== '') {
            $where .= ' AND movipecosacombus.ano_ordencom = :ano ';
            $params[':ano'] = $filters['ano'];
        }

        if ($filters['oficina'] !== '') {
            $where .= ' AND movipecosacombus.oficina_ordencom = :oficina ';
            $params[':oficina'] = substr($filters['oficina'], 0, 3);
        }

        if ($filters['almacen'] !== '') {
            $where .= ' AND movipecosacombus.almacen_ordencom = :almacen ';
            $params[':almacen'] = substr($filters['almacen'], 0, 3);
        }

        if ($filters['fechainicio'] !== '' && $filters['fechafin'] !== '') {
            $where .= " AND (STR_TO_DATE(movipecosacombus.fecha_orden, '%d/%m/%Y') >= STR_TO_DATE(:fi, '%d/%m/%Y') AND STR_TO_DATE(movipecosacombus.fecha_orden, '%d/%m/%Y') <= STR_TO_DATE(:ff, '%d/%m/%Y')) ";
            $params[':fi'] = $filters['fechainicio'];
            $params[':ff'] = $filters['fechafin'];
        }

        $sql = 'SELECT movipecosacombus.norden, movipecosacombus.ano_ordencom as ano, movipecosacombus.fecha_orden as fecha, movipecosacombus.total_ordencom as total, movipecosacombus.estado_ordencom as estado'
            . ' FROM movipecosacombus '
            . $where
            . ' ORDER BY movipecosacombus.fecha_orden DESC, movipecosacombus.norden DESC ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        Response::view('layouts.main', [
            'pageTitle' => 'Reporte — PECOSA combustible',
            'contentView' => 'reportes/almacen/pecosas-combustible',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }

    public function stockFisico(Request $request): void
    {
        $filters = [
            'ano' => $request->query('ano', date('Y')),
            'articulo' => $request->query('articulo', ''),
            'almacen' => $request->query('almacen', ''),
        ];

        $pdo = Database::getInstance()->getConnection();

        $where = ' WHERE 1=1 ';
        $params = [];

        if ($filters['ano'] !== '') {
            $where .= ' AND stock.ano = :ano ';
            $params[':ano'] = $filters['ano'];
        }

        if ($filters['articulo'] !== '') {
            $where .= ' AND stock.articulo = :articulo ';
            $params[':articulo'] = $filters['articulo'];
        }

        if ($filters['almacen'] !== '') {
            $where .= ' AND stock.almacen = :almacen ';
            $params[':almacen'] = substr($filters['almacen'], 0, 3);
        }

        $sql = 'SELECT stock.articulo as codigo, productos.nombre_producto as nombre, stock.cantidading as ingresos, stock.cantidadsal as salidas, stock.saldo, stock.medida, stock.almacen, stock.ano'
            . ' FROM stock '
            . ' JOIN productos ON stock.articulo = productos.codigo_producto '
            . $where
            . ' ORDER BY stock.almacen, stock.articulo DESC ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        Response::view('layouts.main', [
            'pageTitle' => 'Reporte — Stock físico',
            'contentView' => 'reportes/almacen/stock-fisico',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }

    public function kardex(Request $request): void
    {
        $filters = [
            'ano' => $request->query('ano', date('Y')),
            'articulo' => $request->query('articulo', ''),
            'fechainicio' => $request->query('fechainicio', ''),
            'fechafin' => $request->query('fechafin', ''),
        ];

        $pdo = Database::getInstance()->getConnection();

        $where = ' WHERE 1=1 ';
        $params = [];

        if ($filters['ano'] !== '') {
            $where .= ' AND kardex.ano = :ano ';
            $params[':ano'] = $filters['ano'];
        }

        if ($filters['articulo'] !== '') {
            $where .= ' AND kardex.articulo = :articulo ';
            $params[':articulo'] = $filters['articulo'];
        }

        if ($filters['fechainicio'] !== '' && $filters['fechafin'] !== '') {
            $where .= " AND (STR_TO_DATE(kardex.fecha, '%d/%m/%Y') >= STR_TO_DATE(:fi, '%d/%m/%Y') AND STR_TO_DATE(kardex.fecha, '%d/%m/%Y') <= STR_TO_DATE(:ff, '%d/%m/%Y')) ";
            $params[':fi'] = $filters['fechainicio'];
            $params[':ff'] = $filters['fechafin'];
        }

        $sql = 'SELECT kardex.numero as numdoc, kardex.destipodoc as tipodoc, kardex.fecha, kardex.articulo as codigo, kardex.desarticulo as nombre, kardex.cantidading as ingresos, kardex.cantidadsal as salidas, kardex.medida, kardex.estado, kardex.ano'
            . ' FROM kardex '
            . $where
            . ' ORDER BY STR_TO_DATE(kardex.fecha, \'%d/%m/%Y\') ASC ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        Response::view('layouts.main', [
            'pageTitle' => 'Reporte — Kardex',
            'contentView' => 'reportes/almacen/kardex',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }
}
