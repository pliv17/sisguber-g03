<?php
/**
 * views/logistica/cuadro_comparativo.php
 * Vista propia: layout de comparación entre proveedores (no es lista simple).
 */
$yearDefault = (int) date('Y');
?>

<div class="d-flex align-items-center gap-2 mb-3">
    <span class="badge bg-purple fs-6" style="background:#7c3aed!important">CC</span>
    <h1 class="h4 mb-0 text-primary">Cuadro Comparativo</h1>
</div>

<!-- Filtros -->
<div class="card shadow-sm mb-3">
    <div class="card-header py-2">
        <span class="small fw-semibold text-secondary text-uppercase">
            <i class="bi bi-funnel me-1"></i>Parámetros
        </span>
    </div>
    <div class="card-body py-3">
        <div class="row g-2">
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label small mb-1">Periodo</label>
                <select id="cc-year" class="form-select form-select-sm font-monospace">
                    <?php for ($y = $yearDefault + 1; $y >= $yearDefault - 10; $y--): ?>
                    <option value="<?= $y ?>" <?= $y === $yearDefault ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label small mb-1">N° Requerimiento / Proceso</label>
                <input type="text" id="cc-proceso" class="form-control form-control-sm font-monospace" placeholder="REQ-<?= $yearDefault ?>-00000">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label small mb-1">Área solicitante</label>
                <input type="text" id="cc-area" class="form-control form-control-sm" placeholder="Ej. Gerencia de Administración">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label small mb-1">Fecha inicio</label>
                <input type="date" id="cc-fecha-ini" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label small mb-1">Fecha fin</label>
                <input type="date" id="cc-fecha-fin" class="form-control form-control-sm">
            </div>
        </div>
    </div>
    <div class="card-footer py-2 d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" id="cc-btn-generar">
            <i class="bi bi-grid-3x3 me-1"></i>Generar cuadro
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="cc-btn-limpiar">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
        </button>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="cc-btn-csv">
                <i class="bi bi-download me-1"></i>CSV
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="cc-btn-print" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Imprimir
            </button>
        </div>
    </div>
</div>

<!-- Resumen ganadores -->
<div class="card shadow-sm mb-3 d-none" id="cc-card-resumen">
    <div class="card-header py-2">
        <span class="small fw-semibold text-secondary text-uppercase">
            <i class="bi bi-trophy me-1 text-warning"></i>Proveedor con menor precio por ítem
        </span>
    </div>
    <div class="card-body py-2" id="cc-resumen-body"></div>
</div>

<!-- Cuadro comparativo -->
<div class="card shadow-sm d-none" id="cc-card-cuadro">
    <div class="card-header py-2 d-flex align-items-center gap-2">
        <span class="small fw-semibold text-secondary text-uppercase">
            <i class="bi bi-table me-1"></i>Comparativo de cotizaciones —
            <span id="cc-label-proceso" class="font-monospace"></span>
        </span>
        <span class="ms-auto badge bg-secondary" id="cc-label-items">0 ítems</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0" id="cc-table">
            <!-- generada por logistica/comparativo.js -->
        </table>
    </div>
</div>
