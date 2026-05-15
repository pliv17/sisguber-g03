<?php

declare(strict_types=1);

namespace App\Modules\Logistica\Repositories;

use App\Core\Database;
use PDO;

/**
 * BaseLogisticaRepository — Acceso PDO compartido para todos los
 * repositorios del módulo de Logística.
 *
 * REGLA: Nunca concatenes variables de usuario en SQL.
 *        Usa siempre prepared statements.
 */
abstract class BaseLogisticaRepository
{
    protected function pdo(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    /**
     * Ejecuta COUNT(*) sobre una tabla con WHERE opcional y devuelve el total.
     *
     * @param array<string, mixed> $params
     */
    protected function countWhere(string $table, string $where, array $params): int
    {
        $sql  = "SELECT COUNT(*) FROM {$table}" . ($where !== '' ? " WHERE {$where}" : '');
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }
}
