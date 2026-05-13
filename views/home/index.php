<?php
/**
 * views/home/index.php — Vista de bienvenida.
 * Variables disponibles: $appEnv, $phpVersion, $dbStatus
 * REGLA: Solo HTML + e() para escapar. Sin lógica de negocio.
 */
?>

<div class="row mb-4">
    <div class="col">
        <h1 class="h3 fw-bold text-primary">
            <i class="bi bi-box-seam me-2"></i>Sistema de Abastecimiento y Almacén
        </h1>
        <p class="text-muted">Scaffolding base listo. Construye sobre esta estructura.</p>
    </div>
</div>

<!-- Tarjetas de estado ─────────────────────────────── -->
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="bi bi-server fs-4 text-success"></i>
                </div>
                <div>
                    <div class="text-muted small">Base de Datos (PDO)</div>
                    <div class="fw-semibold"><?= e($dbStatus) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-code-slash fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="text-muted small">PHP Version</div>
                    <div class="fw-semibold"><?= e($phpVersion) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                    <i class="bi bi-gear fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="text-muted small">Entorno</div>
                    <div class="fw-semibold"><?= e($appEnv) ?></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Panel Ajax de prueba ───────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-lightning me-2 text-warning"></i>Prueba Ajax
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Verifica que jQuery y la ruta <code>/api/ping</code> responden correctamente.
                </p>
                <button id="btn-ping" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-send me-1"></i>Hacer Ping
                </button>
                <div id="ping-result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-map me-2 text-info"></i>Módulos planificados
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <?php
                    $modulos = [
                        'Maestros Presupuestales' => 'bi-card-list',
                        'Órdenes de Compra / Servicio' => 'bi-cart',
                        'NEA (Nota de Entrada al Almacén)' => 'bi-box-arrow-in-down',
                        'PECOSA / Vales' => 'bi-file-earmark-check',
                        'Stock y Kardex' => 'bi-archive',
                        'Reportes' => 'bi-file-earmark-bar-graph',
                    ];
                    foreach ($modulos as $nombre => $icono): ?>
                        <li class="py-1 border-bottom d-flex align-items-center gap-2">
                            <i class="bi <?= e($icono) ?> text-secondary"></i>
                            <span><?= e($nombre) ?></span>
                            <span class="badge bg-secondary ms-auto">Pendiente</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
