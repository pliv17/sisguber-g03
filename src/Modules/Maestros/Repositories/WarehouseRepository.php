<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;
use PDOException;

final class WarehouseRepository extends BaseMaestroRepository
{
    private const TABLE = 'almacen';

    /**
     * @return array{0: list<array<string, mixed>>, 1: int}
     */
    public function paginate(string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $where = '';
        $params = [];
        if ($q !== '') {
            $where = ' WHERE nombre_almacen LIKE :q ';
            $params[':q'] = '%' . $q . '%';
        }
        $sqlCount = 'SELECT COUNT(*) FROM ' . self::TABLE . $where;
        $stmt = $pdo->prepare($sqlCount);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $sql = 'SELECT codigo_almacen AS code, nombre_almacen AS name, direccion AS address FROM ' . self::TABLE . $where
            . ' ORDER BY codigo_almacen ASC LIMIT :lim OFFSET :off';
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [$stmt->fetchAll(), $total];
    }

    public function find(string $id): ?array
    {
        $stmt = $this->pdo()->prepare(
            'SELECT codigo_almacen AS code, nombre_almacen AS name, direccion AS address FROM ' . self::TABLE . ' WHERE codigo_almacen = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @return int nuevo id
     * @throws PDOException
     */
    public function insert(string $code, string $name, string $address): int
    {
        $stmt = $this->pdo()->prepare(
            'INSERT INTO ' . self::TABLE . ' (codigo_almacen, nombre_almacen, direccion) VALUES (:code, :name, :address)'
        );
        $stmt->execute([
            ':code'    => $code,
            ':name'    => $name,
            ':address' => $address,
        ]);

        return (int) $this->pdo()->lastInsertId();
    }

    public function update(int $id, string $code, string $name, string $address): void
    {
        $stmt = $this->pdo()->prepare(
            'UPDATE ' . self::TABLE . ' SET codigo_almacen = :code, nombre_almacen = :name, direccion = :address WHERE id = :id'
        );
        $stmt->execute([
            ':code'    => $code,
            ':name'    => $name,
            ':address' => $address,
            ':id'      => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo()->prepare('DELETE FROM ' . self::TABLE . ' WHERE id = :id');

        return $stmt->execute([':id' => $id]) && $stmt->rowCount() > 0;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function allForReport(string $q = ''): array
    {
        $params = [];
        $where = '';
        if ($q !== '') {
            $where = ' WHERE nombre_almacen LIKE :q ';
            $params[':q'] = '%' . $q . '%';
        }
        $stmt = $this->pdo()->prepare(
            'SELECT codigo_almacen AS code, nombre_almacen AS name, direccion AS address FROM ' . self::TABLE . $where . ' ORDER BY nombre_almacen ASC'
        );
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
