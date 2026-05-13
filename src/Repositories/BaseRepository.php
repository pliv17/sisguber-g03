<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * BaseRepository — Acceso centralizado a PDO para todos los repositorios.
 *
 * REGLAS DE ORO (obligatorias en toda la capa de datos):
 *
 *   ✅ SIEMPRE usa prepared statements:
 *      $stmt = $this->pdo->prepare('SELECT * FROM tabla WHERE id = :id');
 *      $stmt->execute([':id' => $id]);
 *
 *   ❌ NUNCA concatenes variables de usuario en SQL:
 *      // MAL — vulnerable a SQL Injection:
 *      $pdo->query("SELECT * FROM tabla WHERE id = " . $_POST['id']);
 *
 *   ❌ NUNCA mezcles HTML en esta capa.
 *   ❌ NUNCA uses mysql_* ni mysqli procedural.
 *
 * Ejemplo de repositorio concreto:
 *
 *   namespace App\Repositories;
 *
 *   class AlmacenesRepository extends BaseRepository
 *   {
 *       protected string $table = 'almacenes';
 *
 *       public function findAll(): array
 *       {
 *           $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY nombre");
 *           return $stmt->fetchAll();
 *       }
 *
 *       public function findById(int $id): array|false
 *       {
 *           $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
 *           $stmt->execute([':id' => $id]);
 *           return $stmt->fetch();
 *       }
 *
 *       public function insert(array $data): int
 *       {
 *           $stmt = $this->pdo->prepare(
 *               "INSERT INTO {$this->table} (nombre, activo) VALUES (:nombre, :activo)"
 *           );
 *           $stmt->execute([
 *               ':nombre' => $data['nombre'],
 *               ':activo' => $data['activo'] ?? 1,
 *           ]);
 *           return (int) $this->pdo->lastInsertId();
 *       }
 *   }
 */
abstract class BaseRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }
}
