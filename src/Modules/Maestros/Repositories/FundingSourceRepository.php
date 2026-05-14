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

    public function find(int $id, int $year, string $code): ?array
    {
        $s = $this->pdo()->prepare('SELECT id_fuente AS id, ano_fuente AS year, codigo_fuente AS code, nombre_fuente AS name, NULL AS description FROM ' . self::T . ' WHERE id_fuente=:id AND ano_fuente=:y AND codigo_fuente=:c');
        $s->execute([':id' => $id, ':y' => $year, ':c' => $code]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(int $id, int $year, string $code, string $name, ?string $description): int
    {
        $s = $this->pdo()->prepare('INSERT INTO ' . self::T . ' (id_fuente,ano_fuente,codigo_fuente,nombre_fuente) VALUES (:id,:y,:c,:n)');
        $s->execute([':id' => $id, ':y' => $year, ':c' => $code, ':n' => $name]);

        return $id;
    }

    public function update(int $oldId, int $oldYear, string $oldCode, int $newId, int $newYear, string $newCode, string $newName, ?string $newDescription): void
    {
        $s = $this->pdo()->prepare('UPDATE ' . self::T . ' SET id_fuente=:nid,ano_fuente=:ny,codigo_fuente=:nc,nombre_fuente=:nn WHERE id_fuente=:oid AND ano_fuente=:oy AND codigo_fuente=:oc');
        $s->execute([':nid' => $newId, ':ny' => $newYear, ':nc' => $newCode, ':nn' => $newName, ':oid' => $oldId, ':oy' => $oldYear, ':oc' => $oldCode]);
    }

    public function delete(int $id, int $year, string $code): bool
    {
        $s = $this->pdo()->prepare('DELETE FROM ' . self::T . ' WHERE id_fuente=:id AND ano_fuente=:y AND codigo_fuente=:c');

        return $s->execute([':id' => $id, ':y' => $year, ':c' => $code]) && $s->rowCount() > 0;
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
