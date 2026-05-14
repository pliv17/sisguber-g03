<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;

final class OfficeRepository extends BaseMaestroRepository
{
    private const T = 'oficinas';

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function paginate(string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $w = '';
        $p = [];
        if ($q !== '') {
            $w = ' WHERE nombre_oficina LIKE :q OR codigo_oficina LIKE :q2 ';
            $p[':q'] = '%' . $q . '%';
            $p[':q2'] = '%' . $q . '%';
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM ' . self::T . $w);
        $stmt->execute($p);
        $total = (int) $stmt->fetchColumn();
        $sql = 'SELECT codigo_oficina AS code, nombre_oficina AS name, responsable FROM ' . self::T . $w . ' ORDER BY codigo_oficina LIMIT :lim OFFSET :off';
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
        $s = $this->pdo()->prepare('SELECT codigo_oficina AS code, nombre_oficina AS name, responsable FROM ' . self::T . ' WHERE id=:id');
        $s->execute([':id' => $id]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(string $code, string $name, string $responsible): int
    {
        $s = $this->pdo()->prepare('INSERT INTO ' . self::T . ' (codigo_oficina,nombre_oficina,responsable) VALUES (:c,:n,:r)');
        $s->execute([':c' => $code, ':n' => $name, ':r' => $responsible]);

        return (int) $this->pdo()->lastInsertId();
    }

    public function update(int $id, string $code, string $name, string $responsible): void
    {
        $s = $this->pdo()->prepare('UPDATE ' . self::T . ' SET codigo_oficina=:c,nombre_oficina=:n,responsable=:r WHERE id=:id');
        $s->execute([':c' => $code, ':n' => $name, ':r' => $responsible, ':id' => $id]);
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
            $w = ' WHERE name LIKE :q ';
            $p[':q'] = '%' . $q . '%';
        }
        $s = $this->pdo()->prepare('SELECT codigo_oficina AS code,nombre_oficina AS name,responsable FROM ' . self::T . $w . ' ORDER BY nombre_oficina');
        $s->execute($p);

        return $s->fetchAll();
    }
}
