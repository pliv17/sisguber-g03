<?php
/** @var array<string,mixed> $maestroList */
/** @var string $csrfToken */
$ml = $maestroList;
$yearDefault = (int) date('Y');
?>

<div class="maestro-list-app" id="maestro-app"
     data-config="<?= e(json_encode($ml, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-0 text-primary"><?= e((string) ($ml['heading'] ?? 'Maestros')) ?></h1>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="#" id="maestro-btn-pdf" role="button">
                <i class="bi bi-file-earmark-pdf me-1"></i>Informe PDF
            </a>
            <button type="button" class="btn btn-primary btn-sm" id="maestro-btn-new">
                <i class="bi bi-plus-lg me-1"></i>Nuevo
            </button>
        </div>
    </div>

    <?php if (!empty($ml['year'])): ?>
        <div class="row g-2 mb-3">
            <div class="col-auto">
                <label for="maestro-year" class="form-label small mb-0">Ejercicio</label>
                <select id="maestro-year" class="form-select form-select-sm" style="min-width: 8rem;">
                    <?php for ($y = $yearDefault + 1; $y >= $yearDefault - 15; $y--): ?>
                        <option value="<?= (int) $y ?>" <?= $y === $yearDefault ? 'selected' : '' ?>><?= (int) $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-2 mb-3">
        <div class="col-md-6 col-lg-4">
            <label for="maestro-search" class="form-label small mb-0">Buscar</label>
            <div class="input-group input-group-sm">
                <input type="search" id="maestro-search" class="form-control" autocomplete="off" placeholder="Escriba y espere o pulse Buscar">
                <button class="btn btn-outline-primary" type="button" id="maestro-btn-search">Buscar</button>
            </div>
        </div>
        <div class="col-md-3 col-lg-2 d-flex align-items-end">
            <span class="small text-muted" id="maestro-loading" style="display:none;">
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>Cargando…
            </span>
        </div>
    </div>

    <div class="table-responsive shadow-sm rounded bg-white">
        <table class="table table-sm table-hover align-middle mb-0" id="maestro-table">
            <thead class="table-light">
            <tr id="maestro-thead"></tr>
            </thead>
            <tbody id="maestro-tbody">
            <tr><td class="text-muted p-3">Sin datos.</td></tr>
            </tbody>
        </table>
    </div>

    <nav class="mt-3" aria-label="Paginación">
        <ul class="pagination pagination-sm mb-0" id="maestro-pagination"></ul>
    </nav>

    <!-- Modal alta/edición -->
    <div class="modal fade" id="maestroModal" tabindex="-1" aria-labelledby="maestroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form id="maestro-form" novalidate>
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="maestroModalLabel">Registro</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" id="maestro-modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="maestro-btn-save">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal confirmar borrado -->
    <div class="modal fade" id="maestroDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5">Confirmar eliminación</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">¿Eliminar este registro? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="maestro-btn-confirm-delete">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>
