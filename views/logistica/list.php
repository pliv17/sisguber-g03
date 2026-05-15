<?php
/**
 * views/logistica/list.php — Vista genérica de listado para Logística.
 *
 * Recibe del controlador:
 *   $logisticaList  : array  — configuración completa de la pantalla
 *   $logisticaPage  : string — clave (ej: 'orden-compra')
 *   $csrfToken      : string — token CSRF inyectado por el layout
 *
 * Patrón idéntico al de maestros/list.php:
 *   - PHP escribe data-config con JSON de la config
 *   - JS (logistica/list.js) lee el config y maneja todo via Ajax
 */

/** @var array<string,mixed> $logisticaList */
$ll          = $logisticaList;
$yearDefault = (int) date('Y');
?>

<div class="logistica-list-app" id="logistica-app"
     data-config="<?= e(json_encode($ll, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>">

    <!-- ── Cabecera ─────────────────────────────────────────── -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-<?= e((string)($ll['badge_color'] ?? 'primary')) ?> fs-6">
                <?= e((string)($ll['badge'] ?? '')) ?>
            </span>
            <h1 class="h4 mb-0 text-primary"><?= e((string)($ll['heading'] ?? 'Logística')) ?></h1>
        </div>
        <?php if (!empty($ll['can_create'])): ?>
        <button type="button" class="btn btn-primary btn-sm" id="logistica-btn-new">
            <i class="bi bi-plus-lg me-1"></i>Nuevo
        </button>
        <?php endif; ?>
    </div>

    <!-- ── Filtros ──────────────────────────────────────────── -->
    <div class="card shadow-sm mb-3">
        <div class="card-header py-2 d-flex align-items-center gap-2">
            <i class="bi bi-funnel text-secondary"></i>
            <span class="small fw-semibold text-secondary text-uppercase">Filtros de búsqueda</span>
        </div>
        <div class="card-body py-3">
            <div class="row g-2" id="logistica-filters">

                <?php foreach (($ll['filters'] ?? []) as $filter): ?>
                <?php
                    $fname  = e((string)($filter['name']  ?? ''));
                    $flabel = e((string)($filter['label'] ?? ''));
                    $ftype  = (string)($filter['type'] ?? 'text');
                    $fmono  = !empty($filter['mono']) ? ' font-monospace' : '';
                    $fmax   = !empty($filter['maxlength']) ? ' maxlength="'.(int)$filter['maxlength'].'"' : '';
                ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="lf-<?= $fname ?>" class="form-label small mb-1"><?= $flabel ?></label>

                    <?php if ($ftype === 'year'): ?>
                        <select id="lf-<?= $fname ?>" name="<?= $fname ?>" class="form-select form-select-sm logistica-filter" data-filter="<?= $fname ?>">
                            <?php for ($y = $yearDefault + 1; $y >= $yearDefault - 10; $y--): ?>
                            <option value="<?= $y ?>" <?= $y === $yearDefault ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>

                    <?php elseif ($ftype === 'select'): ?>
                        <select id="lf-<?= $fname ?>" name="<?= $fname ?>" class="form-select form-select-sm logistica-filter" data-filter="<?= $fname ?>">
                            <?php foreach (($filter['options'] ?? []) as $opt): ?>
                            <option value="<?= e((string)$opt['v']) ?>"><?= e((string)$opt['l']) ?></option>
                            <?php endforeach; ?>
                        </select>

                    <?php elseif ($ftype === 'date'): ?>
                        <input type="date" id="lf-<?= $fname ?>" name="<?= $fname ?>"
                               class="form-control form-control-sm logistica-filter<?= $fmono ?>"
                               data-filter="<?= $fname ?>">

                    <?php else: ?>
                        <input type="text" id="lf-<?= $fname ?>" name="<?= $fname ?>"
                               class="form-control form-control-sm logistica-filter<?= $fmono ?>"
                               data-filter="<?= $fname ?>"
                               autocomplete="off"
                               placeholder="<?= $flabel ?>"
                               <?= $fmax ?>>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

            </div><!-- /row filtros -->
        </div>
        <div class="card-footer py-2 d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" id="logistica-btn-search">
                <i class="bi bi-search me-1"></i>Buscar
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="logistica-btn-reset">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
            </button>
            <span class="ms-auto small text-muted d-flex align-items-center" id="logistica-loading" style="display:none!important">
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>Cargando…
            </span>
        </div>
    </div>

    <!-- ── Tabla de resultados ──────────────────────────────── -->
    <div class="card shadow-sm">
        <div class="card-header py-2 d-flex align-items-center gap-2">
            <i class="bi bi-table text-secondary"></i>
            <span class="small fw-semibold text-secondary text-uppercase">
                Resultados
            </span>
            <span class="ms-auto badge bg-secondary" id="logistica-count">—</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" id="logistica-table">
                <thead class="table-light">
                    <tr id="logistica-thead"></tr>
                </thead>
                <tbody id="logistica-tbody">
                    <tr><td class="text-muted p-3">Sin datos.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer py-2">
            <nav aria-label="Paginación">
                <ul class="pagination pagination-sm mb-0" id="logistica-pagination"></ul>
            </nav>
        </div>
    </div>

    <!-- ── Modal: anular ────────────────────────────────────── -->
    <div class="modal fade" id="logisticaCancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5">
                        <i class="bi bi-slash-circle text-danger me-2"></i>Confirmar anulación
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        ¿Confirma la anulación de la orden
                        <strong id="cancel-norden-label"></strong>?
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="logistica-btn-confirm-cancel">
                        <i class="bi bi-slash-circle me-1"></i>Anular
                    </button>
                </div>
            </div>
        </div>
    </div>

</div><!-- /#logistica-app -->
