<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;

final class BudgetItemRepository extends BaseMaestroRepository
{
    private const T = 'partidas';

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function paginate(int $year, string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $w = ' WHERE ano_partida = :y ';
        $p = [':y' => $year];
        if ($q !== '') {
            $w .= ' AND (nombre_partida LIKE :q OR codigo_partida LIKE :q2) ';
            $p[':q'] = '%' . $q . '%';
            $p[':q2'] = '%' . $q . '%';
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM ' . self::T . $w);
        $stmt->execute($p);
        $total = (int) $stmt->fetchColumn();
        $sql = 'SELECT id_partida AS id, ano_partida AS year, codigo_partida AS code, nombre_partida AS name FROM ' . self::T . $w . ' ORDER BY codigo_partida LIMIT :lim OFFSET :off';
        $stmt = $pdo->prepare($sql);
        foreach ($p as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [$stmt->fetchAll(), $total];
    }

    public function find(int $id, int $year, string $code): ?array
    {
        $s = $this->pdo()->prepare('SELECT id_partida AS id, ano_partida AS year, codigo_partida AS code, nombre_partida AS name FROM ' . self::T . ' WHERE id_partida=:id AND ano_partida=:y AND codigo_partida=:c');
        $s->execute([':id' => $id, ':y' => $year, ':c' => $code]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(int $id, int $year, string $code, string $name): int
    {
        $s = $this->pdo()->prepare('INSERT INTO ' . self::T . ' (id_partida,ano_partida,codigo_partida,nombre_partida) VALUES (:id,:y,:c,:n)');
        $s->execute([':id' => $id, ':y' => $year, ':c' => $code, ':n' => $name]);

        return $id;
    }

    public function update(int $oldId, int $oldYear, string $oldCode, int $newId, int $newYear, string $newCode, string $newName): void
    {
        $s = $this->pdo()->prepare('UPDATE ' . self::T . ' SET id_partida=:nid,ano_partida=:ny,codigo_partida=:nc,nombre_partida=:nn WHERE id_partida=:oid AND ano_partida=:oy AND codigo_partida=:oc');
        $s->execute([':nid' => $newId, ':ny' => $newYear, ':nc' => $newCode, ':nn' => $newName, ':oid' => $oldId, ':oy' => $oldYear, ':oc' => $oldCode]);
    }

    public function delete(int $id, int $year, string $code): bool
    {
        $s = $this->pdo()->prepare('DELETE FROM ' . self::T . ' WHERE id_partida=:id AND ano_partida=:y AND codigo_partida=:c');

        return $s->execute([':id' => $id, ':y' => $year, ':c' => $code]) && $s->rowCount() > 0;
    }

    /** @return list<array<string,mixed>> */
    public function allForReport(int $year, string $q = ''): array
    {
        $p = [':y' => $year];
        $w = ' WHERE ano_partida = :y ';
        if ($q !== '') {
            $w .= ' AND (nombre_partida LIKE :q OR codigo_partida LIKE :q2) ';
            $p[':q'] = '%' . $q . '%';
            $p[':q2'] = '%' . $q . '%';
        }
        $s = $this->pdo()->prepare('SELECT ano_partida AS year, codigo_partida AS code, nombre_partida AS name FROM ' . self::T . $w . ' ORDER BY codigo_partida');
        $s->execute($p);

        return $s->fetchAll();
    }
}
