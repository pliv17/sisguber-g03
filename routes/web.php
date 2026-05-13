<?php

declare(strict_types=1);

/**
 * routes/web.php — Registro de rutas de la aplicación.
 *
 * Sintaxis: $router->get('/uri', [Controller::class, 'metodo']);
 *           $router->post('/uri', [Controller::class, 'metodo']);
 */

use App\Controllers\AcercaController;
use App\Controllers\Almacen\AlmacenController;
use App\Controllers\ApiController;
use App\Controllers\HomeController;
use App\Controllers\Logistica\LogisticaController;
use App\Controllers\Maestros\MaestrosController;
use App\Controllers\Reportes\ReportesAlmacenController;
use App\Controllers\Reportes\ReportesController;
use App\Controllers\Reportes\ReportesLogisticaController;
use App\Controllers\Utilidades\ClavesController;
use App\Controllers\Utilidades\MigracionSiafController;
use App\Controllers\Utilidades\NeasManualesController;
use App\Controllers\Utilidades\PecosasManualesController;
use App\Controllers\Utilidades\RespaldoController;

// ── Página de bienvenida ────────────────────────────────────────
$router->get('/', [HomeController::class, 'index']);

// ── Salud del sistema ───────────────────────────────────────────
$router->get('/health', [ApiController::class, 'health']);

// ── API interna (Ajax) ──────────────────────────────────────────
$router->get('/api/ping', [ApiController::class, 'ping']);

// ══════════════════════════════════════════════════════════════════
// Maestros (archivos maestros)
// ══════════════════════════════════════════════════════════════════
$router->get('/maestros/almacenes', [MaestrosController::class, 'almacenes']);
$router->get('/maestros/unidades-medida', [MaestrosController::class, 'unidadesMedida']);
$router->get('/maestros/rubros-proveedor', [MaestrosController::class, 'rubrosProveedor']);
$router->get('/maestros/proveedores', [MaestrosController::class, 'proveedores']);
$router->get('/maestros/catalogo/bienes', [MaestrosController::class, 'catalogoBienes']);
$router->get('/maestros/catalogo/servicios', [MaestrosController::class, 'catalogoServicios']);
$router->get('/maestros/oficinas', [MaestrosController::class, 'oficinas']);
$router->get('/maestros/fuentes-financiamiento', [MaestrosController::class, 'fuentesFinanciamiento']);
$router->get('/maestros/metas', [MaestrosController::class, 'metas']);
$router->get('/maestros/partidas', [MaestrosController::class, 'partidas']);

// ══════════════════════════════════════════════════════════════════
// Logística (órdenes + cuadros)
// ══════════════════════════════════════════════════════════════════
$router->get('/ordenes/compra', [LogisticaController::class, 'ordenCompra']);
$router->get('/ordenes/servicio', [LogisticaController::class, 'ordenServicio']);
$router->get('/logistica/cuadro-comparativo', [LogisticaController::class, 'cuadroComparativo']);
$router->get('/logistica/cuadro-necesidades', [LogisticaController::class, 'cuadroNecesidades']);

// ══════════════════════════════════════════════════════════════════
// Almacén
// ══════════════════════════════════════════════════════════════════
$router->get('/almacen/notas-entrada', [AlmacenController::class, 'notasEntrada']);
$router->get('/almacen/pecosas', [AlmacenController::class, 'pecosas']);
$router->get('/almacen/pecosas-combustible', [AlmacenController::class, 'pecosasCombustible']);
$router->get('/almacen/vales-combustible', [AlmacenController::class, 'valesCombustible']);
$router->get('/almacen/stock', [AlmacenController::class, 'stock']);
$router->get('/almacen/kardex', [AlmacenController::class, 'kardex']);

// ══════════════════════════════════════════════════════════════════
// Reportes
// ══════════════════════════════════════════════════════════════════
$router->get('/reportes', [ReportesController::class, 'index']);
$router->get('/reportes/logistica/ordenes-compra', [ReportesLogisticaController::class, 'ordenesCompra']);
$router->get('/reportes/logistica/ordenes-servicio', [ReportesLogisticaController::class, 'ordenesServicio']);
$router->get('/reportes/almacen/notas-entrada', [ReportesAlmacenController::class, 'notasEntrada']);
$router->get('/reportes/almacen/pecosas', [ReportesAlmacenController::class, 'pecosas']);
$router->get('/reportes/almacen/pecosas-combustible', [ReportesAlmacenController::class, 'pecosasCombustible']);
$router->get('/reportes/almacen/stock-fisico', [ReportesAlmacenController::class, 'stockFisico']);
$router->get('/reportes/almacen/kardex', [ReportesAlmacenController::class, 'kardex']);

// ══════════════════════════════════════════════════════════════════
// Utilidades
// ══════════════════════════════════════════════════════════════════
$router->get('/utilidades/respaldo', [RespaldoController::class, 'index']);
$router->get('/utilidades/respaldo/crear', [RespaldoController::class, 'crear']);
$router->get('/utilidades/respaldo/restaurar', [RespaldoController::class, 'restaurar']);
$router->get('/utilidades/neas-manuales', [NeasManualesController::class, 'index']);
$router->get('/utilidades/pecosas-manuales', [PecosasManualesController::class, 'index']);
$router->get('/utilidades/claves', [ClavesController::class, 'index']);
$router->get('/utilidades/migracion-siaf/metas', [MigracionSiafController::class, 'metas']);
$router->get('/utilidades/migracion-siaf/fuentes', [MigracionSiafController::class, 'fuentes']);
$router->get('/utilidades/migracion-siaf/partidas', [MigracionSiafController::class, 'partidas']);

// ══════════════════════════════════════════════════════════════════
// Acerca de
// ══════════════════════════════════════════════════════════════════
$router->get('/acerca-de/creditos', [AcercaController::class, 'creditos']);
