<?php

declare(strict_types=1);

namespace App\Modules\Logistica\Repositories;

use PDO;

/**
 * ServiceOrderRepository — Órdenes de Servicio.
 * Misma estructura que PurchaseOrderRepository, tabla: moviservicio
 */
final class ServiceOrderRepository extends BaseLogisticaRepository
{
    /**
     * @param array<string, mixed> $filters
     * @return array{0: list<array<string,mixed>>, 1: int}
     */
    public function paginate(array $filters, int $offset, int $limit): array
    {
        [$where, $params] = $this->buildWhere($filters);

        $from = "FROM moviservicio os
                 INNER JOIN proveedores p ON os.proveedor_os = p.ruc_provee
                 {$where}";

        $total = $this->countRaw("SELECT COUNT(*) {$from}", $params);

        $sql = "SELECT
                    os.norden_os           AS norden,
                    os.ano_os              AS year,
                    os.fecha_os            AS fecha,
                    p.nombre_provee        AS proveedor,
                    p.ruc_provee           AS ruc,
                    os.total_os            AS total,
                    os.estado_os           AS estado
                {$from}
                ORDER BY os.norden_os DESC
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
            "SELECT os.norden_os AS norden, os.ano_os AS year, os.fecha_os AS fecha,
                    p.ruc_provee AS ruc, p.nombre_provee AS proveedor,
                    os.total_os AS total, os.estado_os AS estado,
                    os.oficina_os AS oficina
             FROM moviservicio os
             INNER JOIN proveedores p ON os.proveedor_os = p.ruc_provee
             WHERE os.norden_os = :n AND os.ano_os = :y
             LIMIT 1"
        );
        $stmt->execute([':n' => $norden, ':y' => $year]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function cancel(string $norden, int $year): bool
    {
        $stmt = $this->pdo()->prepare(
            "UPDATE moviservicio SET estado_os = 2
             WHERE norden_os = :n AND ano_os = :y AND estado_os = 1"
        );
        $stmt->execute([':n' => $norden, ':y' => $year]);

        return $stmt->rowCount() > 0;
    }

    public function insert(array $data): string
    {
        $norden = $this->nextNorden((int) $data['year']);
        $stmt   = $this->pdo()->prepare(
            "INSERT INTO moviservicio
                (norden_os, ano_os, fecha_os, proveedor_os, oficina_os, total_os, estado_os)
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

    /** @return list<array<string,mixed>> */
    public function allForReport(array $filters): array
    {
        [$where, $params] = $this->buildWhere($filters);
        $stmt = $this->pdo()->prepare(
            "SELECT os.norden_os AS norden, os.ano_os AS year, os.fecha_os AS fecha,
                    p.nombre_provee AS proveedor, os.total_os AS total, os.estado_os AS estado
             FROM moviservicio os
             INNER JOIN proveedores p ON os.proveedor_os = p.ruc_provee
             {$where}
             ORDER BY os.norden_os DESC"
        );
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // ── Helpers ─────────────────────────────────────────────────

    /** @return array{0: string, 1: array<string,mixed>} */
    private function buildWhere(array $f): array
    {
        $cond   = [];
        $params = [];

        $year = (int) ($f['year'] ?? date('Y'));
        $cond[]          = 'os.ano_os = :year';
        $params[':year'] = $year;

        if (!empty($f['ruc'])) {
            $cond[]         = 'os.proveedor_os = :ruc';
            $params[':ruc'] = $f['ruc'];
        }
        if (!empty($f['q'])) {
            $cond[]       = 'p.nombre_provee LIKE :q';
            $params[':q'] = '%' . $f['q'] . '%';
        }
        if (!empty($f['estado']) && (int) $f['estado'] > 0) {
            $cond[]            = 'os.estado_os = :estado';
            $params[':estado'] = (int) $f['estado'];
        }
        if (!empty($f['norden'])) {
            $cond[]            = 'os.norden_os = :norden';
            $params[':norden'] = $f['norden'];
        }
        if (!empty($f['fecha_ini'])) {
            $cond[]      = "STR_TO_DATE(os.fecha_os,'%d/%m/%Y') >= STR_TO_DATE(:fi,'%d/%m/%Y')";
            $params[':fi'] = $f['fecha_ini'];
        }
        if (!empty($f['fecha_fin'])) {
            $cond[]      = "STR_TO_DATE(os.fecha_os,'%d/%m/%Y') <= STR_TO_DATE(:ff,'%d/%m/%Y')";
            $params[':ff'] = $f['fecha_fin'];
        }

        $where = $cond !== [] ? 'WHERE ' . implode(' AND ', $cond) : '';

        return [$where, $params];
    }

    private function nextNorden(int $year): string
    {
        $stmt = $this->pdo()->prepare(
            "SELECT MAX(CAST(norden_os AS UNSIGNED)) FROM moviservicio WHERE ano_os = :y"
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
