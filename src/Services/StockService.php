<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * StockService — Servicio para consultas del módulo Stock.
 *
 * Responsabilidades:
 *   - Recuperar datos de la tabla 'stock' desde la BD
 *   - Aplicar filtros y búsquedas
 *   - Mapear resultados a formato de vista
 */
class StockService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene registros de stock con filtros opcionales.
     *
     * @param array<string, mixed> $filters Filtros opcionales:
     *   - 'ano' (string): Año fiscal
     *   - 'almacen' (string): Código almacén
     *   - 'articulo' (string): Código o descripción de artículo
     *   - 'estado' (string): Estado del artículo
     *
     * @return array<int, array<string, mixed>>
     */
    public function getStockRecords(array $filters = []): array
    {
        $query = 'SELECT 
                    ano, 
                    almacen, 
                    articulo, 
                    desarticulo, 
                    medida, 
                    cantidading, 
                    cantidadsal, 
                    saldo, 
                    marca, 
                    serie, 
                    estado, 
                    preciopromedio, 
                    importetotal 
                  FROM stock 
                  WHERE 1=1';

        $params = [];

        // Filtro por año
        if (!empty($filters['ano'])) {
            $query .= ' AND ano = :ano';
            $params[':ano'] = $filters['ano'];
        }

        // Filtro por almacén
        if (!empty($filters['almacen'])) {
            $query .= ' AND almacen = :almacen';
            $params[':almacen'] = $filters['almacen'];
        }

        // Filtro por artículo (búsqueda en código o descripción)
        if (!empty($filters['articulo'])) {
            $search = '%' . $filters['articulo'] . '%';
            $query .= ' AND (articulo LIKE :articulo OR desarticulo LIKE :articulo_desc)';
            $params[':articulo'] = $search;
            $params[':articulo_desc'] = $search;
        }

        // Filtro por estado
        if (!empty($filters['estado'])) {
            $query .= ' AND estado = :estado';
            $params[':estado'] = $filters['estado'];
        }

        // Orden y límite
        $query .= ' ORDER BY ano DESC, almacen ASC, articulo ASC LIMIT 500';

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            // Log error si es necesario
            return [];
        }
    }

    /**
     * Obtiene el año más reciente disponible en stock.
     *
     * @return string
     */
    public function getLatestYear(): string
    {
        try {
            $stmt = $this->pdo->prepare('SELECT ano FROM stock ORDER BY ano DESC LIMIT 1');
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['ano'] ?? date('Y');
        } catch (\Exception $e) {
            return date('Y');
        }
    }

    /**
     * Obtiene todos los años disponibles en el sistema (desde 2012).
     * Utiliza la tabla 'anos' en lugar de 'stock' para tener rango completo.
     *
     * @return array<int, string>
     */
    public function getAvailableYears(): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT ano FROM anos WHERE ano >= 2012 ORDER BY ano DESC');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_column($rows, 'ano');
        } catch (\Exception $e) {
            // Fallback: años desde 2012 hasta hoy
            $years = [];
            $current = date('Y');
            for ($year = $current; $year >= 2012; $year--) {
                $years[] = (string) $year;
            }
            return $years;
        }
    }

    /**
     * Obtiene todos los almacenes disponibles.
     *
     * @return array<int, array<string, string>>
     */
    public function getAvailableWarehouses(): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT DISTINCT almacen FROM stock ORDER BY almacen ASC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtiene estadísticas del stock.
     *
     * @return array<string, mixed>
     */
    public function getStockStats(): array
    {
        try {
            $query = 'SELECT 
                        COUNT(*) as total_items,
                        SUM(importetotal) as total_value,
                        COUNT(DISTINCT almacen) as total_warehouses,
                        COUNT(CASE WHEN estado = "ACTIVO" THEN 1 END) as active_items
                      FROM stock';

            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'total_items' => (int) ($result['total_items'] ?? 0),
                'total_value' => (float) ($result['total_value'] ?? 0),
                'total_warehouses' => (int) ($result['total_warehouses'] ?? 0),
                'active_items' => (int) ($result['active_items'] ?? 0),
            ];
        } catch (\Exception $e) {
            return [
                'total_items' => 0,
                'total_value' => 0,
                'total_warehouses' => 0,
                'active_items' => 0,
            ];
        }
    }
}
