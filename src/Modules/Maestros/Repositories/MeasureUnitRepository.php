<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;

final class MeasureUnitRepository extends BaseMaestroRepository
{
    private const TABLE = 'medida';

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function paginate(string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $where = '';
        $params = [];
        if ($q !== '') {
            $where = ' WHERE nombre_medida LIKE :q ';
            $params[':q'] = '%' . $q . '%';
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM ' . self::TABLE . $where);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();
        $sql = 'SELECT codigo_medida AS code, nombre_medida AS name FROM ' . self::TABLE . $where
            . ' ORDER BY codigo_medida ASC LIMIT :lim OFFSET :off';
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [$stmt->fetchAll(), $total];
    }

    public function find(string $code): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT codigo_medida AS code, nombre_medida AS name FROM ' . self::TABLE . ' WHERE codigo_medida = :code');
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function insert(string $code, string $name): void
    {
        $stmt = $this->pdo()->prepare('INSERT INTO ' . self::TABLE . ' (codigo_medida, nombre_medida) VALUES (:c,:n)');
        $stmt->execute([':c' => $code, ':n' => $name]);
    }

    public function update(string $oldCode, string $newCode, string $newName): void
    {
        $stmt = $this->pdo()->prepare('UPDATE ' . self::TABLE . ' SET codigo_medida=:nc, nombre_medida=:nn WHERE codigo_medida=:oc');
        $stmt->execute([':nc' => $newCode, ':nn' => $newName, ':oc' => $oldCode]);
    }

    public function delete(string $code): bool
    {
        $stmt = $this->pdo()->prepare('DELETE FROM ' . self::TABLE . ' WHERE codigo_medida=:code');

        return $stmt->execute([':code' => $code]) && $stmt->rowCount() > 0;
    }

    /** @return list<array<string,mixed>> */
    public function allForReport(string $q = ''): array
    {
        $params = [];
        $where = '';
        if ($q !== '') {
            $where = ' WHERE nombre_medida LIKE :q ';
            $params[':q'] = '%' . $q . '%';
        }
        $stmt = $this->pdo()->prepare('SELECT codigo_medida AS code, nombre_medida AS name FROM ' . self::TABLE . $where . ' ORDER BY codigo_medida');
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
