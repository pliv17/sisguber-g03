<?php
/** @var string $pageTitle */
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="h3 fw-bold text-primary">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>Reportes generales
        </h1>
        <p class="text-muted mb-0">Accesos rápidos a listados e impresiones por área.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-truck me-2 text-primary"></i>Logística
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <a href="<?= e(url('/reportes/logistica/ordenes-compra')) ?>" class="text-decoration-none">Listado de órdenes de compra</a>
                </li>
                <li class="list-group-item">
                    <a href="<?= e(url('/reportes/logistica/ordenes-servicio')) ?>" class="text-decoration-none">Listado de órdenes de servicio</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-archive me-2 text-primary"></i>Almacén
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="<?= e(url('/reportes/almacen/notas-entrada')) ?>">Reporte de notas de entrada (NEA)</a></li>
                <li class="list-group-item"><a href="<?= e(url('/reportes/almacen/pecosas')) ?>">Reporte de PECOSA</a></li>
                <li class="list-group-item"><a href="<?= e(url('/reportes/almacen/pecosas-combustible')) ?>">Reporte de PECOSA combustible</a></li>
                <li class="list-group-item"><a href="<?= e(url('/reportes/almacen/stock-fisico')) ?>">Listado de stock físico por producto</a></li>
                <li class="list-group-item"><a href="<?= e(url('/reportes/almacen/kardex')) ?>">Kardex diario por producto</a></li>
            </ul>
        </div>
    </div>
</div>
