<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;

final class CatalogProductRepository extends BaseMaestroRepository
{
    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function paginate(string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $w = '';
        $p = [];
        if ($q !== '') {
            $w = ' WHERE p.nombre_producto LIKE :q OR p.codigo_producto LIKE :q2 ';
            $p[':q'] = '%' . $q . '%';
            $p[':q2'] = '%' . $q . '%';
        }
        $from = ' FROM productos p ' . $w;
        $stmt = $pdo->prepare('SELECT COUNT(*) ' . $from);
        $stmt->execute($p);
        $total = (int) $stmt->fetchColumn();
        $sql = 'SELECT p.codigo_producto AS code, p.nombre_producto AS name, p.medida_producto AS measure_unit_id, p.medida_producto AS measure_unit_code '
            . $from . ' ORDER BY p.codigo_producto LIMIT :lim OFFSET :off';
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
            'SELECT p.codigo_producto AS code, p.nombre_producto AS name, p.medida_producto AS measure_unit_id, p.medida_producto AS measure_unit_code '
            . 'FROM productos p WHERE p.id=:id'
        );
        $s->execute([':id' => $id]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(string $code, string $name, int $measureUnitId): int
    {
        $s = $this->pdo()->prepare('INSERT INTO productos (codigo_producto,nombre_producto,medida_producto) VALUES (:c,:n,:m)');
        $s->execute([':c' => $code, ':n' => $name, ':m' => $measureUnitId]);

        return (int) $this->pdo()->lastInsertId();
    }

    public function update(int $id, string $code, string $name, int $measureUnitId): void
    {
        $s = $this->pdo()->prepare('UPDATE productos SET codigo_producto=:c,nombre_producto=:n,medida_producto=:m WHERE id=:id');
        $s->execute([':c' => $code, ':n' => $name, ':m' => $measureUnitId, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $s = $this->pdo()->prepare('DELETE FROM catalog_products WHERE id=:id');

        return $s->execute([':id' => $id]) && $s->rowCount() > 0;
    }

    /** @return list<array<string,mixed>> */
    public function allForReport(string $q = ''): array
    {
        $p = [];
        $w = '';
        if ($q !== '') {
            $w = ' WHERE p.name LIKE :q OR p.code LIKE :q2 ';
            $p[':q'] = '%' . $q . '%';
            $p[':q2'] = '%' . $q . '%';
        }
        $s = $this->pdo()->prepare(
            'SELECT p.codigo_producto AS code, p.nombre_producto AS name, p.medida_producto AS um FROM productos p '
            . $w . ' ORDER BY p.nombre_producto'
        );
        $s->execute($p);

        return $s->fetchAll();
    }
}
