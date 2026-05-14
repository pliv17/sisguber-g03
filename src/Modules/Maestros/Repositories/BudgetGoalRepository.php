<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;

final class BudgetGoalRepository extends BaseMaestroRepository
{
    private const T = 'metas';

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function paginate(int $year, string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $w = ' WHERE ano_meta = :y ';
        $p = [':y' => $year];
        if ($q !== '') {
            $w .= ' AND nombre_meta LIKE :q ';
            $p[':q'] = '%' . $q . '%';
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM ' . self::T . $w);
        $stmt->execute($p);
        $total = (int) $stmt->fetchColumn();
        $sql = 'SELECT id_meta AS id, ano_meta AS year, codigo_meta AS code, nombre_meta AS name, cadena_meta AS description FROM ' . self::T . $w . ' ORDER BY nombre_meta LIMIT :lim OFFSET :off';
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
        $s = $this->pdo()->prepare('SELECT id_meta AS id, ano_meta AS year, codigo_meta AS code, nombre_meta AS name, cadena_meta AS description FROM ' . self::T . ' WHERE id_meta=:id AND ano_meta=:y AND codigo_meta=:c');
        $s->execute([':id' => $id, ':y' => $year, ':c' => $code]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(int $id, int $year, string $code, string $name, ?string $description): int
    {
        $s = $this->pdo()->prepare('INSERT INTO ' . self::T . ' (id_meta,ano_meta,codigo_meta,nombre_meta,cadena_meta) VALUES (:id,:y,:c,:n,:d)');
        $s->execute([':id' => $id, ':y' => $year, ':c' => $code, ':n' => $name, ':d' => $description]);

        return $id;
    }

    public function update(int $oldId, int $oldYear, string $oldCode, int $newId, int $newYear, string $newCode, string $newName, ?string $newDescription): void
    {
        $s = $this->pdo()->prepare('UPDATE ' . self::T . ' SET id_meta=:nid,ano_meta=:ny,codigo_meta=:nc,nombre_meta=:nn,cadena_meta=:nd WHERE id_meta=:oid AND ano_meta=:oy AND codigo_meta=:oc');
        $s->execute([':nid' => $newId, ':ny' => $newYear, ':nc' => $newCode, ':nn' => $newName, ':nd' => $newDescription, ':oid' => $oldId, ':oy' => $oldYear, ':oc' => $oldCode]);
    }

    public function delete(int $id, int $year, string $code): bool
    {
        $s = $this->pdo()->prepare('DELETE FROM ' . self::T . ' WHERE id_meta=:id AND ano_meta=:y AND codigo_meta=:c');

        return $s->execute([':id' => $id, ':y' => $year, ':c' => $code]) && $s->rowCount() > 0;
    }

    /** @return list<array<string,mixed>> */
    public function allForReport(int $year, string $q = ''): array
    {
        $p = [':y' => $year];
        $w = ' WHERE ano_meta = :y ';
        if ($q !== '') {
            $w .= ' AND nombre_meta LIKE :q ';
            $p[':q'] = '%' . $q . '%';
        }
        $s = $this->pdo()->prepare('SELECT ano_meta AS year, codigo_meta AS code, nombre_meta AS name, cadena_meta AS description FROM ' . self::T . $w . ' ORDER BY nombre_meta');
        $s->execute($p);

        return $s->fetchAll();
    }
}
