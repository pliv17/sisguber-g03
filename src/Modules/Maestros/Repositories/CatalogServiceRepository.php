<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;

final class CatalogServiceRepository extends BaseMaestroRepository
{
    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function paginate(string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $w = '';
        $p = [];
        if ($q !== '') {
            $w = ' WHERE s.nombre_servicio LIKE :q OR s.codigo_servicio LIKE :q2 ';
            $p[':q'] = '%' . $q . '%';
            $p[':q2'] = '%' . $q . '%';
        }
        $from = ' FROM servicios s ' . $w;
        $stmt = $pdo->prepare('SELECT COUNT(*) ' . $from);
        $stmt->execute($p);
        $total = (int) $stmt->fetchColumn();
        $sql = 'SELECT s.codigo_servicio AS code, s.nombre_servicio AS name, s.medida_servicio AS measure_unit_id, s.medida_servicio AS measure_unit_code '
            . $from . ' ORDER BY s.codigo_servicio LIMIT :lim OFFSET :off';
        $stmt = $pdo->prepare($sql);
        foreach ($p as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [$stmt->fetchAll(), $total];
    }

    public function find(int $id): ?array
    {
        $s = $this->pdo()->prepare(
            'SELECT s.codigo_servicio AS code, s.nombre_servicio AS name, s.medida_servicio AS measure_unit_id, s.medida_servicio AS measure_unit_code '
            . 'FROM servicios s WHERE s.codigo_servicio=:id'
        );
        $s->execute([':id' => $id]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(string $code, string $name, int $measureUnitId): int
    {
        $s = $this->pdo()->prepare('INSERT INTO servicios (codigo_servicio,nombre_servicio,medida_servicio) VALUES (:c,:n,:m)');
        $s->execute([':c' => $code, ':n' => $name, ':m' => $measureUnitId]);

        return (int) $this->pdo()->lastInsertId();
    }

    public function update(int $id, string $code, string $name, int $measureUnitId): void
    {
        $s = $this->pdo()->prepare('UPDATE servicios SET codigo_servicio=:c,nombre_servicio=:n,medida_servicio=:m WHERE id=:id');
        $s->execute([':c' => $code, ':n' => $name, ':m' => $measureUnitId, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $s = $this->pdo()->prepare('DELETE FROM catalog_services WHERE id=:id');

        return $s->execute([':id' => $id]) && $s->rowCount() > 0;
    }

    /** @return list<array<string,mixed>> */
    public function allForReport(string $q = ''): array
    {
        $p = [];
        $w = '';
        if ($q !== '') {
            $w = ' WHERE s.name LIKE :q OR s.code LIKE :q2 ';
            $p[':q'] = '%' . $q . '%';
            $p[':q2'] = '%' . $q . '%';
        }
        $s = $this->pdo()->prepare(
            'SELECT s.codigo_servicio AS code, s.nombre_servicio AS name, s.medida_servicio AS um FROM servicios s '
            . $w . ' ORDER BY s.codigo_servicio'
        );
        $s->execute($p);

        return $s->fetchAll();
    }
}
