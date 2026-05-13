<?php

declare(strict_types=1);

namespace App\Services;

/**
 * BaseService — Clase base opcional para servicios.
 *
 * Los Services contienen la lógica de negocio:
 *   - Validaciones de reglas de negocio
 *   - Cálculos (saldos, totales, stock)
 *   - Orquestación de múltiples Repositories
 *   - Transformación de datos antes de enviar a la vista
 *
 * NUNCA pongas SQL directamente aquí; delega a los Repositories.
 * NUNCA pongas HTML aquí.
 *
 * Ejemplo de módulo Maestros → Almacenes:
 *
 *   namespace App\Services;
 *   use App\Repositories\AlmacenesRepository;
 *
 *   class AlmacenesService
 *   {
 *       public function __construct(
 *           private AlmacenesRepository $repo = new AlmacenesRepository()
 *       ) {}
 *
 *       public function listar(): array
 *       {
 *           return $this->repo->findAll();
 *       }
 *
 *       public function guardar(array $datos): int
 *       {
 *           // Validar reglas de negocio
 *           if (empty($datos['nombre'])) {
 *               throw new \InvalidArgumentException('El nombre del almacén es requerido.');
 *           }
 *           return $this->repo->insert($datos);
 *       }
 *   }
 */
abstract class BaseService
{
    // Espacio reservado — los servicios concretos no necesitan extender esto,
    // pero puede usarse para inyectar dependencias comunes.
}
