<?php

declare(strict_types=1);

/**
 * routes/api.php — API JSON (prefijo /api/maestros/...).
 * Requiere $router instanciado (ver public/index.php).
 */

use App\Modules\Maestros\Controllers\Api\BudgetGoalApiController;
use App\Modules\Maestros\Controllers\Api\BudgetItemApiController;
use App\Modules\Maestros\Controllers\Api\CatalogProductApiController;
use App\Modules\Maestros\Controllers\Api\CatalogServiceApiController;
use App\Modules\Maestros\Controllers\Api\FundingSourceApiController;
use App\Modules\Maestros\Controllers\Api\MeasureUnitApiController;
use App\Modules\Maestros\Controllers\Api\OfficeApiController;
use App\Modules\Maestros\Controllers\Api\SupplierApiController;
use App\Modules\Maestros\Controllers\Api\SupplierCategoryApiController;
use App\Modules\Maestros\Controllers\Api\WarehouseApiController;

/** @var \App\Core\Router $router */

$registerCrud = static function (string $base, string $controller) use ($router): void {
    $router->get($base . '/report', [$controller, 'report']);
    $router->get($base, [$controller, 'index']);
    $router->get($base . '/{id}', [$controller, 'show']);
    $router->post($base, [$controller, 'store']);
    $router->put($base . '/{id}', [$controller, 'update']);
    $router->patch($base . '/{id}', [$controller, 'update']);
    $router->delete($base . '/{id}', [$controller, 'destroy']);
};

$registerCrud('/api/maestros/almacenes', WarehouseApiController::class);
$registerCrud('/api/maestros/unidades-medida', MeasureUnitApiController::class);
$registerCrud('/api/maestros/rubros-proveedor', SupplierCategoryApiController::class);
$registerCrud('/api/maestros/oficinas', OfficeApiController::class);
$registerCrud('/api/maestros/productos', CatalogProductApiController::class);
$registerCrud('/api/maestros/servicios', CatalogServiceApiController::class);

// Fuentes / metas / partidas (filtro año en query)
$registerCrud('/api/maestros/fuentes-financiamiento', FundingSourceApiController::class);
$registerCrud('/api/maestros/metas', BudgetGoalApiController::class);
$registerCrud('/api/maestros/partidas', BudgetItemApiController::class);

// Proveedores — PK string RUC en la URL
$baseP = '/api/maestros/proveedores';
$router->get($baseP . '/report', [SupplierApiController::class, 'report']);
$router->get($baseP, [SupplierApiController::class, 'index']);
$router->get($baseP . '/{ruc}', [SupplierApiController::class, 'show']);
$router->post($baseP, [SupplierApiController::class, 'store']);
$router->put($baseP . '/{ruc}', [SupplierApiController::class, 'update']);
$router->patch($baseP . '/{ruc}', [SupplierApiController::class, 'update']);
$router->delete($baseP . '/{ruc}', [SupplierApiController::class, 'destroy']);
