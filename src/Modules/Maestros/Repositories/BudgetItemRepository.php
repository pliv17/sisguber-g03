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

    public function find(int $id): ?array
    {
        $s = $this->pdo()->prepare('SELECT id_partida AS id, ano_partida AS year, codigo_partida AS code, nombre_partida AS name FROM ' . self::T . ' WHERE id_partida=:id');
        $s->execute([':id' => $id]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(int $year, string $code, string $name): int
    {
        $s = $this->pdo()->prepare('INSERT INTO ' . self::T . ' (ano_partida,codigo_partida,nombre_partida) VALUES (:y,:c,:n)');
        $s->execute([':y' => $year, ':c' => $code, ':n' => $name]);

        return (int) $this->pdo()->lastInsertId();
    }

    public function update(int $id, int $year, string $code, string $name): void
    {
        $s = $this->pdo()->prepare('UPDATE ' . self::T . ' SET ano_partida=:y,codigo_partida=:c,nombre_partida=:n WHERE id_partida=:id');
        $s->execute([':y' => $year, ':c' => $code, ':n' => $name, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $s = $this->pdo()->prepare('DELETE FROM ' . self::T . ' WHERE id=:id');

        return $s->execute([':id' => $id]) && $s->rowCount() > 0;
    }

    /** @return list<array<string,mixed>> */
    public function allForReport(int $year, string $q = ''): array
    {
        $p = [':y' => $year];
        $w = ' WHERE year = :y ';
        if ($q !== '') {
            $w .= ' AND (name LIKE :q OR code LIKE :q2) ';
            $p[':q'] = '%' . $q . '%';
            $p[':q2'] = '%' . $q . '%';
        }
        $s = $this->pdo()->prepare('SELECT ano_partida AS year, codigo_partida AS code, nombre_partida AS name FROM ' . self::T . $w . ' ORDER BY codigo_partida');
        $s->execute($p);

        return $s->fetchAll();
    }
}
