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
<body class="d-flex flex-column min-vh-100" data-current-path="<?= e(request_path()) ?>">

<!-- ══════════════════════════════════════════
     Cabecera: menú + ubicación (la ruta va FUERA de <nav> para no tapar los dropdowns)
     ══════════════════════════════════════════ -->
<header class="app-top-header bg-primary shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-dark py-0" aria-label="Principal">
        <div class="container-fluid d-flex flex-wrap align-items-center py-2">
            <a class="navbar-brand fw-bold mb-0" href="<?= e(url('/')) ?>">
                <i class="bi bi-box-seam me-2"></i>Abastecimiento
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMain"
                aria-controls="navbarMain"
                aria-expanded="false"
                aria-label="Alternar menú de navegación"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse w-100" id="navbarMain">
                <?php require BASE_PATH . '/views/partials/nav-main.php'; ?>
            </div>
        </div>
    </nav>

    <div
        class="nav-context-bar border-top border-white border-opacity-25 w-100 py-2 px-3 px-lg-4 small d-none d-md-flex flex-wrap align-items-center justify-content-between gap-2"
        role="status"
        aria-label="Ubicación en la aplicación"
    >
        <div class="text-white-50 d-flex align-items-center gap-2 min-w-0 flex-grow-1">
            <i class="bi bi-signpost-2 flex-shrink-0" aria-hidden="true"></i>
            <code class="nav-context-path text-white-50 small mb-0 text-truncate"><?= e(request_path()) ?></code>
        </div>
        <?php
        $_nav_ctx = nav_context_title($pageTitle ?? null);
        if ($_nav_ctx !== ''): ?>
            <div class="text-white text-opacity-90 text-truncate flex-shrink-0 nav-context-title" title="<?= e($_nav_ctx) ?>">
                <?= e($_nav_ctx) ?>
            </div>
        <?php endif; ?>
    </div>
</header>
<!-- /CABECERA -->

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
    src="https://code.jquery.com/jquery-3.7.1.min.js?v=1"
    crossorigin="anonymous"
></script>
<!-- Bootstrap 5 JS Bundle (incluye Popper) -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js?v=1"
    crossorigin="anonymous"
></script>
<!-- URL base para Ajax (misma origen que APP_URL) -->
<script>window.APP_URL_BASE = <?= json_encode(rtrim($_ENV['APP_URL'] ?? '', '/'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<!-- JS propio -->
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
