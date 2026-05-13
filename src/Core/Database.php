<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database — Conexión PDO centralizada (Singleton).
 *
 * REGLAS DE ORO:
 *   1. NUNCA concatenes variables de usuario en las consultas SQL.
 *      Usa siempre prepared statements: $pdo->prepare() + execute([]).
 *   2. NUNCA mezcles HTML con esta capa.
 *   3. NUNCA uses mysql_* ni mysqli procedural; solo este PDO.
 *
 * Ejemplo de uso:
 *   $pdo = Database::getInstance()->getConnection();
 *   $stmt = $pdo->prepare('SELECT * FROM almacenes WHERE id = :id');
 *   $stmt->execute([':id' => $id]);
 *   $row = $stmt->fetch();
 */
class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host    = $_ENV['DB_HOST']   ?? '127.0.0.1';
        $port    = $_ENV['DB_PORT']   ?? '3306';
        $dbname  = $_ENV['DB_NAME']   ?? '';
        $user    = $_ENV['DB_USER']   ?? 'root';
        $pass    = $_ENV['DB_PASS']   ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                // Lanza excepciones en errores SQL
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Prepared statements reales (no emulados) — más seguro
                PDO::ATTR_EMULATE_PREPARES   => false,
                // Devuelve filas como arrays asociativos por defecto
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Conexión persistente — útil bajo carga moderada
                PDO::ATTR_PERSISTENT         => false,
            ]);
        } catch (PDOException $e) {
            // En producción no expongas el mensaje completo
            $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $msg   = $debug ? $e->getMessage() : 'Error de conexión a la base de datos.';
            throw new \RuntimeException($msg, (int) $e->getCode(), $e);
        }
    }

    /**
     * Devuelve la única instancia (Singleton thread-safe para PHP).
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Evitar clonación y deserialización del Singleton
    private function __clone() {}
    public function __wakeup(): never
    {
        throw new \RuntimeException('Cannot unserialize Database singleton.');
    }
}
