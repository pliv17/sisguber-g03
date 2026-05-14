<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;

final class SupplierCategoryRepository extends BaseMaestroRepository
{
    private const T = 'rubro';

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function paginate(string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $where = '';
        $p = [];
        if ($q !== '') {
            $where = ' WHERE nombre_rubro LIKE :q ';
            $p[':q'] = '%' . $q . '%';
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM ' . self::T . $where);
        $stmt->execute($p);
        $total = (int) $stmt->fetchColumn();
        $sql = 'SELECT codigo_rubro AS code, nombre_rubro AS description FROM ' . self::T . $where . ' ORDER BY nombre_rubro LIMIT :lim OFFSET :off';
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
        $s = $this->pdo()->prepare('SELECT codigo_rubro AS code, nombre_rubro AS description FROM ' . self::T . ' WHERE id=:id');
        $s->execute([':id' => $id]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(string $code, string $description): int
    {
        $s = $this->pdo()->prepare('INSERT INTO ' . self::T . ' (codigo_rubro,nombre_rubro) VALUES (:c,:d)');
        $s->execute([':c' => $code, ':d' => $description]);

        return (int) $this->pdo()->lastInsertId();
    }

    public function update(int $id, string $code, string $description): void
    {
        $s = $this->pdo()->prepare('UPDATE ' . self::T . ' SET codigo_rubro=:c,nombre_rubro=:d WHERE id=:id');
        $s->execute([':c' => $code, ':d' => $description, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $s = $this->pdo()->prepare('DELETE FROM ' . self::T . ' WHERE id=:id');

        return $s->execute([':id' => $id]) && $s->rowCount() > 0;
    }

    /** @return list<array<string,mixed>> */
    public function allForReport(string $q = ''): array
    {
        $p = [];
        $w = '';
        if ($q !== '') {
            $w = ' WHERE description LIKE :q ';
            $p[':q'] = '%' . $q . '%';
        }
        $s = $this->pdo()->prepare('SELECT codigo_rubro AS code,nombre_rubro AS description FROM ' . self::T . $w . ' ORDER BY nombre_rubro');
        $s->execute($p);

        return $s->fetchAll();
    }
}
