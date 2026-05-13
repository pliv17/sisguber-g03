<?php

declare(strict_types=1);

/**
 * views/layouts/main.php — Layout principal HTML5.
 *
 * Variables esperadas (desde el controlador vía Response::view):
 *   $pageTitle   : string  — Título de la pestaña
 *   $contentView : string  — Ruta de la vista parcial (ej: 'home/index')
 *   + cualquier variable adicional de la vista hija
 *
 * REGLA: Este archivo solo maneja estructura HTML, sin lógica de negocio.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Abastecimiento y Almacén">
    <title><?= e($pageTitle ?? 'Sistema de Abastecimiento') ?></title>

    <!-- Bootstrap 5.3 CSS (CDN con fallback local posible) -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
    <!-- CSS propio -->
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>

<!-- ══════════════════════════════════════════
     NAVBAR principal
     ══════════════════════════════════════════ -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">

        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="<?= e(url('/')) ?>">
            <i class="bi bi-box-seam me-2"></i>Abastecimiento
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMain"
            aria-controls="navbarMain"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="<?= e(url('/')) ?>">
                        <i class="bi bi-house me-1"></i>Inicio
                    </a>
                </li>

                <!-- Maestros -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-card-list me-1"></i>Maestros
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="dropdown-item-text text-muted small">Presupuestales</span></li>
                        <li><a class="dropdown-item" href="<?= e(url('/maestros/almacenes')) ?>">Almacenes</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('/maestros/unidades')) ?>">Unidades de Medida</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><span class="dropdown-item-text text-muted small">Logístico</span></li>
                        <li><a class="dropdown-item" href="<?= e(url('/maestros/proveedores')) ?>">Proveedores</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('/maestros/articulos')) ?>">Artículos / Bienes</a></li>
                    </ul>
                </li>

                <!-- Abastecimiento -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-cart me-1"></i>Abastecimiento
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= e(url('/ordenes/compra')) ?>">Órdenes de Compra</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('/ordenes/servicio')) ?>">Órdenes de Servicio</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('/nea')) ?>">NEA</a></li>
                    </ul>
                </li>

                <!-- Almacén -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-archive me-1"></i>Almacén
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= e(url('/almacen/pecosa')) ?>">PECOSA</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('/almacen/vales')) ?>">Vales</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('/almacen/stock')) ?>">Stock</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('/almacen/kardex')) ?>">Kardex</a></li>
                    </ul>
                </li>

                <!-- Reportes -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= e(url('/reportes')) ?>">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i>Reportes
                    </a>
                </li>

            </ul>

            <!-- Lado derecho de la navbar -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= e(url('/health')) ?>" target="_blank" title="Estado del sistema">
                        <i class="bi bi-heart-pulse me-1"></i>Health
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- /NAVBAR -->

<!-- ══════════════════════════════════════════
     CONTENIDO PRINCIPAL
     ══════════════════════════════════════════ -->
<main class="container-fluid py-4 px-4">
    <?php
    // Incluir la vista específica de cada controlador
    $__viewFile = BASE_PATH . '/views/' . str_replace('.', '/', $contentView ?? 'errors/missing') . '.php';
    if (file_exists($__viewFile)) {
        require $__viewFile;
    } else {
        echo '<div class="alert alert-danger">Vista no encontrada: ' . e($contentView ?? '') . '</div>';
    }
    ?>
</main>
<!-- /CONTENIDO -->

<!-- ══════════════════════════════════════════
     FOOTER
     ══════════════════════════════════════════ -->
<footer class="footer bg-light border-top mt-auto py-3">
    <div class="container-fluid text-center text-muted small">
        Sistema de Abastecimiento y Almacén &mdash;
        PHP <?= e(PHP_VERSION) ?> &bull;
        Entorno: <strong><?= e($_ENV['APP_ENV'] ?? 'n/a') ?></strong>
    </div>
</footer>

<!-- jQuery 3.x -->
<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"
></script>
<!-- Bootstrap 5 JS Bundle (incluye Popper) -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmAYEAqTbcBNopGFn5Q6dpLqFoGv"
    crossorigin="anonymous"
></script>
<!-- JS propio -->
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
