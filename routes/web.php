<?php

declare(strict_types=1);

/**
 * routes/web.php — Registro de rutas de la aplicación.
 *
 * Sintaxis: $router->get('/uri', [Controller::class, 'metodo']);
 *           $router->post('/uri', [Controller::class, 'metodo']);
 *
 * ─────────────────────────────────────────────────────────────────
 * CÓMO AÑADIR UN NUEVO MÓDULO (ej. Maestros → Almacenes):
 *
 *   1. Crea src/Controllers/Maestros/AlmacenesController.php
 *   2. Crea src/Services/AlmacenesService.php
 *   3. Crea src/Repositories/AlmacenesRepository.php
 *   4. Crea views/maestros/almacenes/index.php (+ crear, editar, etc.)
 *   5. Registra las rutas aquí:
 *
 *      $router->get('/maestros/almacenes',           [AlmacenesController::class, 'index']);
 *      $router->get('/maestros/almacenes/crear',     [AlmacenesController::class, 'crear']);
 *      $router->post('/maestros/almacenes/guardar',  [AlmacenesController::class, 'guardar']);
 *      $router->get('/maestros/almacenes/editar',    [AlmacenesController::class, 'editar']);
 *      $router->post('/maestros/almacenes/actualizar',[AlmacenesController::class, 'actualizar']);
 *      $router->post('/maestros/almacenes/eliminar', [AlmacenesController::class, 'eliminar']);
 * ─────────────────────────────────────────────────────────────────
 */

use App\Controllers\HomeController;
use App\Controllers\ApiController;

// ── Página de bienvenida ────────────────────────────────────────
$router->get('/', [HomeController::class, 'index']);

// ── Salud del sistema ───────────────────────────────────────────
$router->get('/health', [ApiController::class, 'health']);

// ── API interna (Ajax) ──────────────────────────────────────────
$router->get('/api/ping', [ApiController::class, 'ping']);

// ══════════════════════════════════════════════════════════════════
// TODO: Aquí irán las rutas de cada módulo cuando se implementen
// ══════════════════════════════════════════════════════════════════

// Módulo: Maestros Presupuestales
// $router->get('/maestros/almacenes', [App\Controllers\Maestros\AlmacenesController::class, 'index']);

// Módulo: Órdenes de Compra
// $router->get('/ordenes/compra', [App\Controllers\Ordenes\CompraController::class, 'index']);

// Módulo: Stock / Kardex
// $router->get('/almacen/stock', [App\Controllers\Almacen\StockController::class, 'index']);
