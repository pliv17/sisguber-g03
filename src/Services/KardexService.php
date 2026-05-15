<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * KardexService — Servicio para consultas del módulo Kardex.
 *
 * Responsabilidades:
 *   - Recuperar datos de la tabla 'kardex' desde la BD
 *   - Aplicar filtros y búsquedas
 *   - Mapear resultados a formato de vista
 */
class KardexService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene registros de kardex con filtros opcionales.
     *
     * @param array<string, mixed> $filters Filtros opcionales:
     *   - 'ano' (string): Año fiscal
     *   - 'almacen' (string): Código almacén
     *   - 'articulo' (string): Código o descripción de artículo
     *   - 'tipodoc' (string): Tipo de documento
     *
     * @return array<int, array<string, mixed>>
     */
    public function getKardexRecords(array $filters = []): array
    {
        $query = 'SELECT 
                    ano, 
                    fecha, 
                    tipodoc, 
                    destipodoc, 
                    numero, 
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
                  FROM kardex 
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

        // Filtro por tipo de documento
        if (!empty($filters['tipodoc'])) {
            $query .= ' AND tipodoc = :tipodoc';
            $params[':tipodoc'] = $filters['tipodoc'];
        }

        // Orden y límite
        $query .= ' ORDER BY ano DESC, fecha DESC, numero DESC LIMIT 500';

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
     * Obtiene el año más reciente disponible en kardex.
     *
     * @return string
     */
    public function getLatestYear(): string
    {
        try {
            $stmt = $this->pdo->prepare('SELECT ano FROM kardex ORDER BY ano DESC LIMIT 1');
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['ano'] ?? date('Y');
        } catch (\Exception $e) {
            return date('Y');
        }
    }

    /**
     * Obtiene todos los años disponibles en el sistema (desde 2012).
     * Utiliza la tabla 'anos' en lugar de 'kardex' para tener rango completo.
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
            $stmt = $this->pdo->prepare('SELECT DISTINCT almacen FROM kardex WHERE almacen IS NOT NULL AND almacen != "" ORDER BY almacen ASC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtiene estadísticas del kardex.
     *
     * @return array<string, int|float>
     */
    public function getKardexStats(): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT 
                    COUNT(*) as total_movements,
                    COUNT(DISTINCT articulo) as total_articles,
                    COUNT(DISTINCT almacen) as total_warehouses,
                    COALESCE(SUM(importetotal), 0) as total_value
                FROM kardex
            ');
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_movements' => (int) ($row['total_movements'] ?? 0),
                'total_articles' => (int) ($row['total_articles'] ?? 0),
                'total_warehouses' => (int) ($row['total_warehouses'] ?? 0),
                'total_value' => (float) ($row['total_value'] ?? 0),
            ];
        } catch (\Exception $e) {
            return [
                'total_movements' => 0,
                'total_articles' => 0,
                'total_warehouses' => 0,
                'total_value' => 0,
            ];
        }
    }
}
