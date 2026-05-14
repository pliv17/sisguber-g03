<?php

declare(strict_types=1);

namespace App\Modules\Logistica\Repositories;

use PDO;

/**
 * PurchaseOrderRepository — CRUD y listado de Órdenes de Compra.
 * Tabla principal: movicompras
 * Tablas relacionadas: proveedores, lineasoc
 */
final class PurchaseOrderRepository extends BaseLogisticaRepository
{
    private const T = 'movicompras';

    /**
     * Listado paginado con filtros.
     *
     * @param array<string, mixed> $filters  {q, year, ruc, estado, fecha_ini, fecha_fin}
     * @return array{0: list<array<string,mixed>>, 1: int}
     */
    public function paginate(array $filters, int $offset, int $limit): array
    {
        [$where, $params] = $this->buildWhere($filters);

        $from = "FROM movicompras oc
                 INNER JOIN proveedores p ON oc.proveedor_ordencom = p.ruc_provee
                 {$where}";

        $total = (int) $this->pdo()
            ->prepare("SELECT COUNT(*) {$from}")
            ->execute($params) ? $this->countRaw("SELECT COUNT(*) {$from}", $params) : 0;

        $sql = "SELECT
                    oc.norden,
                    oc.ano_ordencom        AS year,
                    oc.fecha_orden         AS fecha,
                    p.nombre_provee        AS proveedor,
                    p.ruc_provee           AS ruc,
                    oc.total_ordencom      AS total,
                    oc.estado_ordencom     AS estado
                {$from}
                ORDER BY oc.norden DESC
                LIMIT :lim OFFSET :off";

        $stmt = $this->pdo()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [$stmt->fetchAll(), $total];
    }

    /** @return array<string,mixed>|null */
    public function find(string $norden, int $year): ?array
    {
        $stmt = $this->pdo()->prepare(
            "SELECT oc.norden, oc.ano_ordencom AS year, oc.fecha_orden AS fecha,
                    p.ruc_provee AS ruc, p.nombre_provee AS proveedor,
                    p.direccion_provee AS direccion,
                    oc.total_ordencom AS total, oc.estado_ordencom AS estado,
                    oc.oficina_ordencom AS oficina
             FROM movicompras oc
             INNER JOIN proveedores p ON oc.proveedor_ordencom = p.ruc_provee
             WHERE oc.norden = :n AND oc.ano_ordencom = :y
             LIMIT 1"
        );
        $stmt->execute([':n' => $norden, ':y' => $year]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** @return list<array<string,mixed>> */
    public function lines(string $norden, int $year): array
    {
        $stmt = $this->pdo()->prepare(
            "SELECT l.articulo_lineaoc AS codigo, l.des_lineaoc AS descripcion,
                    l.medida_lineaoc AS medida, l.cantidad_lineaoc AS cantidad,
                    l.precio_lineaoc AS precio, l.importe_lineaoc AS importe
             FROM lineasoc l
             WHERE l.norden = :n AND l.ano_lineaoc = :y
             ORDER BY l.id_lineaoc ASC"
        );
        $stmt->execute([':n' => $norden, ':y' => $year]);

        return $stmt->fetchAll();
    }

    public function insert(array $data): string
    {
        $norden = $this->nextNorden((int) $data['year']);
        $stmt   = $this->pdo()->prepare(
            "INSERT INTO movicompras
                (norden, ano_ordencom, fecha_orden, proveedor_ordencom,
                 oficina_ordencom, total_ordencom, estado_ordencom)
             VALUES (:n, :y, :f, :p, :o, :t, 1)"
        );
        $stmt->execute([
            ':n' => $norden,
            ':y' => $data['year'],
            ':f' => $data['fecha'],
            ':p' => $data['ruc'],
            ':o' => $data['oficina'],
            ':t' => $data['total'] ?? 0,
        ]);

        return $norden;
    }

    public function cancel(string $norden, int $year): bool
    {
        $stmt = $this->pdo()->prepare(
            "UPDATE movicompras SET estado_ordencom = 2
             WHERE norden = :n AND ano_ordencom = :y AND estado_ordencom = 1"
        );
        $stmt->execute([':n' => $norden, ':y' => $year]);

        return $stmt->rowCount() > 0;
    }

    /** @return list<array<string,mixed>> */
    public function allForReport(array $filters): array
    {
        [$where, $params] = $this->buildWhere($filters);
        $stmt = $this->pdo()->prepare(
            "SELECT oc.norden, oc.ano_ordencom AS year, oc.fecha_orden AS fecha,
                    p.nombre_provee AS proveedor, oc.total_ordencom AS total,
                    oc.estado_ordencom AS estado
             FROM movicompras oc
             INNER JOIN proveedores p ON oc.proveedor_ordencom = p.ruc_provee
             {$where}
             ORDER BY oc.norden DESC"
        );
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // ── Helpers privados ────────────────────────────────────────

    /** @return array{0: string, 1: array<string,mixed>} */
    private function buildWhere(array $f): array
    {
        $conditions = [];
        $params     = [];

        $year = (int) ($f['year'] ?? date('Y'));
        $conditions[] = 'oc.ano_ordencom = :year';
        $params[':year'] = $year;

        if (!empty($f['ruc'])) {
            $conditions[]    = 'oc.proveedor_ordencom = :ruc';
            $params[':ruc']  = $f['ruc'];
        }
        if (!empty($f['q'])) {
            $conditions[]   = 'p.nombre_provee LIKE :q';
            $params[':q']   = '%' . $f['q'] . '%';
        }
        if (!empty($f['estado']) && (int) $f['estado'] > 0) {
            $conditions[]       = 'oc.estado_ordencom = :estado';
            $params[':estado']  = (int) $f['estado'];
        }
        if (!empty($f['norden'])) {
            $conditions[]       = 'oc.norden = :norden';
            $params[':norden']  = $f['norden'];
        }
        if (!empty($f['fecha_ini'])) {
            $conditions[]           = "STR_TO_DATE(oc.fecha_orden,'%d/%m/%Y') >= STR_TO_DATE(:fi,'%d/%m/%Y')";
            $params[':fi']          = $f['fecha_ini'];
        }
        if (!empty($f['fecha_fin'])) {
            $conditions[]           = "STR_TO_DATE(oc.fecha_orden,'%d/%m/%Y') <= STR_TO_DATE(:ff,'%d/%m/%Y')";
            $params[':ff']          = $f['fecha_fin'];
        }

        $where = $conditions !== [] ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return [$where, $params];
    }

    private function nextNorden(int $year): string
    {
        $stmt = $this->pdo()->prepare(
            "SELECT MAX(CAST(norden AS UNSIGNED)) FROM movicompras WHERE ano_ordencom = :y"
        );
        $stmt->execute([':y' => $year]);
        $max = (int) $stmt->fetchColumn();

        return str_pad((string) ($max + 1), 8, '0', STR_PAD_LEFT);
    }

    private function countRaw(string $sql, array $params): int
    {
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }
}
