<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;

final class FundingSourceRepository extends BaseMaestroRepository
{
    private const T = 'fuente';

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function paginate(int $year, string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $w = ' WHERE ano_fuente = :y ';
        $p = [':y' => $year];
        if ($q !== '') {
            $w .= ' AND nombre_fuente LIKE :q ';
            $p[':q'] = '%' . $q . '%';
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM ' . self::T . $w);
        $stmt->execute($p);
        $total = (int) $stmt->fetchColumn();
        $sql = 'SELECT id_fuente AS id, ano_fuente AS year, codigo_fuente AS code, nombre_fuente AS name, NULL AS description FROM ' . self::T . $w
            . ' ORDER BY codigo_fuente LIMIT :lim OFFSET :off';
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
        $s = $this->pdo()->prepare('SELECT id_fuente AS id, ano_fuente AS year, codigo_fuente AS code, nombre_fuente AS name, NULL AS description FROM ' . self::T . ' WHERE id_fuente=:id');
        $s->execute([':id' => $id]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(int $year, string $code, string $name, ?string $description): int
    {
        $s = $this->pdo()->prepare('INSERT INTO ' . self::T . ' (ano_fuente,codigo_fuente,nombre_fuente) VALUES (:y,:c,:n)');
        $s->execute([':y' => $year, ':c' => $code, ':n' => $name]);

        return (int) $this->pdo()->lastInsertId();
    }

    public function update(int $id, int $year, string $code, string $name, ?string $description): void
    {
        $s = $this->pdo()->prepare('UPDATE ' . self::T . ' SET ano_fuente=:y,codigo_fuente=:c,nombre_fuente=:n WHERE id_fuente=:id');
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
        $w = ' WHERE ano_fuente = :y ';
        if ($q !== '') {
            $w .= ' AND nombre_fuente LIKE :q ';
            $p[':q'] = '%' . $q . '%';
        }
        $s = $this->pdo()->prepare('SELECT ano_fuente AS year, codigo_fuente AS code, nombre_fuente AS name, NULL AS description FROM ' . self::T . $w . ' ORDER BY codigo_fuente');
        $s->execute($p);

        return $s->fetchAll();
    }
}
