<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Repositories;

use PDO;

final class SupplierRepository extends BaseMaestroRepository
{
    private const T = 'proveedores';

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function paginate(string $q, int $offset, int $limit): array
    {
        $pdo = $this->pdo();
        $where = '';
        $p = [];
        if ($q !== '') {
            $where = ' WHERE nombre_provee LIKE :q ';
            $p[':q'] = '%' . $q . '%';
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM ' . self::T . $where);
        $stmt->execute($p);
        $total = (int) $stmt->fetchColumn();
        $sql = 'SELECT ruc_provee AS ruc, nombre_provee AS name, direccion_provee AS address FROM ' . self::T . $where . ' ORDER BY nombre_provee LIMIT :lim OFFSET :off';
        $stmt = $pdo->prepare($sql);
        foreach ($p as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [$stmt->fetchAll(), $total];
    }

    public function findByRuc(string $ruc): ?array
    {
        $s = $this->pdo()->prepare('SELECT ruc_provee AS ruc, nombre_provee AS name, direccion_provee AS address FROM ' . self::T . ' WHERE ruc_provee=:ruc');
        $s->execute([':ruc' => $ruc]);
        $r = $s->fetch();

        return $r === false ? null : $r;
    }

    public function insert(string $ruc, string $name, string $address): void
    {
        $s = $this->pdo()->prepare('INSERT INTO ' . self::T . ' (ruc_provee,nombre_provee,direccion_provee) VALUES (:r,:n,:a)');
        $s->execute([':r' => $ruc, ':n' => $name, ':a' => $address]);
    }

    public function update(string $ruc, string $name, string $address): void
    {
        $s = $this->pdo()->prepare('UPDATE ' . self::T . ' SET nombre_provee=:n, direccion_provee=:a WHERE ruc_provee=:r');
        $s->execute([':n' => $name, ':a' => $address, ':r' => $ruc]);
    }

    public function delete(string $ruc): bool
    {
        $s = $this->pdo()->prepare('DELETE FROM ' . self::T . ' WHERE ruc_provee=:r');

        return $s->execute([':r' => $ruc]) && $s->rowCount() > 0;
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
        $s = $this->pdo()->prepare('SELECT ruc_provee AS ruc, nombre_provee AS name, direccion_provee AS address FROM ' . self::T . $w . ' ORDER BY nombre_provee');
        $s->execute($p);

        return $s->fetchAll();
    }
}
