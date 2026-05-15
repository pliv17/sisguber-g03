<?php
/**
 * views/logistica/cuadro_necesidades.php
 * Vista propia: listado con tabs (Lista / Detalle por ítem).
 */
$yearDefault = (int) date('Y');
?>

<div class="d-flex align-items-center gap-2 mb-3">
    <span class="badge bg-warning text-dark fs-6">CN</span>
    <h1 class="h4 mb-0 text-primary">Cuadro de Necesidades</h1>
</div>

<!-- KPIs (pobladas por JS vía API) -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body py-2">
                <div class="small text-muted">Requerimientos</div>
                <div class="fs-4 fw-bold text-primary" id="cn-kpi-total">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body py-2">
                <div class="small text-muted">Pendientes</div>
                <div class="fs-4 fw-bold text-warning" id="cn-kpi-pend">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body py-2">
                <div class="small text-muted">En proceso</div>
                <div class="fs-4 fw-bold text-info" id="cn-kpi-proc">—</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body py-2">
                <div class="small text-muted">Atendidos</div>
                <div class="fs-4 fw-bold text-success" id="cn-kpi-aten">—</div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow-sm mb-3">
    <div class="card-header py-2">
        <span class="small fw-semibold text-secondary text-uppercase">
            <i class="bi bi-funnel me-1"></i>Filtros
        </span>
    </div>
    <div class="card-body py-3">
        <div class="row g-2">
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label small mb-1">Periodo</label>
                <select id="cn-year" class="form-select form-select-sm">
                    <?php for ($y = $yearDefault + 1; $y >= $yearDefault - 10; $y--): ?>
                    <option value="<?= $y ?>" <?= $y === $yearDefault ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label small mb-1">Área / Oficina</label>
                <input type="text" id="cn-area" class="form-control form-control-sm" placeholder="Buscar área...">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label small mb-1">Estado</label>
                <select id="cn-estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="proceso">En Proceso</option>
                    <option value="atendido">Atendido</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label small mb-1">N° Requerimiento</label>
                <input type="text" id="cn-nreq" class="form-control form-control-sm font-monospace" placeholder="REQ-<?= $yearDefault ?>-00000">
            </div>
        </div>
    </div>
    <div class="card-footer py-2 d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" id="cn-btn-buscar">
            <i class="bi bi-search me-1"></i>Filtrar
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="cn-btn-reset">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
        </button>
    </div>
</div>

<!-- Tabs -->
<div class="card shadow-sm" id="cn-card-lista">
    <div class="card-header p-0">
        <ul class="nav nav-tabs card-header-tabs" id="cn-tabs">
            <li class="nav-item">
                <button class="nav-link active px-3 py-2" data-cn-tab="lista">
                    <i class="bi bi-list-ul me-1"></i>Lista de requerimientos
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link px-3 py-2" data-cn-tab="detalle" id="cn-tab-detalle">
                    <i class="bi bi-card-list me-1"></i>Detalle por ítem
                </button>
            </li>
        </ul>
    </div>

    <!-- TAB: Lista -->
    <div id="cn-panel-lista">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" id="cn-table-lista">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>N° Requerimiento</th>
                        <th>Área Solicitante</th>
                        <th>Responsable</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Ítems</th>
                        <th class="text-end">Valor Est. (S/)</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Progreso</th>
                        <th class="text-center" style="width:4rem">Ver</th>
                    </tr>
                </thead>
                <tbody id="cn-tbody-lista">
                    <tr><td colspan="10" class="text-muted p-3">Sin datos.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer py-2">
            <nav><ul class="pagination pagination-sm mb-0" id="cn-pagination"></ul></nav>
        </div>
    </div>

    <!-- TAB: Detalle -->
    <div id="cn-panel-detalle" class="d-none">
        <div class="card-body py-2 border-bottom">
            <div class="alert alert-info alert-sm py-2 mb-0 small" id="cn-detalle-info">
                Seleccione un requerimiento en la pestaña "Lista" para ver su detalle.
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" id="cn-table-detalle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th class="text-center">U/M</th>
                        <th class="text-center">Cant. Solic.</th>
                        <th class="text-center">Cant. Atend.</th>
                        <th class="text-end">P. Unit. Est.</th>
                        <th class="text-end">Total Est.</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody id="cn-tbody-detalle">
                    <tr><td colspan="9" class="text-muted p-3">Sin datos.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
