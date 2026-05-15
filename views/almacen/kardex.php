<!-- Kardex de Productos -->
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= e(url('/')) ?>" class="text-decoration-none">Abastecimiento</a></li>
                    <li class="breadcrumb-item active">Kardex de productos</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-2"><?= e($moduleTitle) ?></h1>
                    <p class="text-muted mb-0"><?= e($moduleLead) ?></p>
                </div>
                <div class="d-flex gap-2">
                    <?php if (!empty($moduleStats)): ?>
                        <?php foreach ($moduleStats as $stat): ?>
                            <div class="card border-0 bg-light p-3 text-center" style="min-width: 120px;">
                                <small class="text-muted d-block"><?= e($stat['label']) ?></small>
                                <strong class="h6 mb-0"><?= e($stat['value']) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Filters and Results -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h5 fw-semibold mb-1">Consulta de movimientos</h2>
                            <p class="text-muted small mb-0">Filtros, búsqueda y acciones para kardex de productos.</p>
                        </div>
                        <span class="badge text-bg-primary-subtle text-primary border border-primary-subtle">Endpoint /almacen/kardex</span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="get" action="<?= e(url('/almacen/kardex')) ?>" class="vstack gap-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="ano" class="form-label fw-semibold small text-uppercase text-muted">Período</label>
                                <select id="ano" name="ano" class="form-select">
                                    <?php foreach ($availableYears ?? range((int) date('Y'), (int) date('Y') - 5) as $year): ?>
                                        <option value="<?= e($year) ?>" <?= (($filters['ano'] ?? '') == (string) $year) ? 'selected' : '' ?>><?= e($year) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="almacen" class="form-label fw-semibold small text-uppercase text-muted">Almacén</label>
                                <input id="almacen" name="almacen" type="text" class="form-control" maxlength="3" value="<?= e($filters['almacen'] ?? '') ?>" placeholder="001">
                            </div>
                            <div class="col-md-4">
                                <label for="articulo" class="form-label fw-semibold small text-uppercase text-muted">Artículo/Descripción</label>
                                <input id="articulo" name="articulo" type="text" class="form-control" maxlength="100" value="<?= e($filters['articulo'] ?? '') ?>" placeholder="ART-0001 o descripción">
                            </div>
                            <div class="col-md-2">
                                <label for="tipodoc" class="form-label fw-semibold small text-uppercase text-muted">Tipo Doc</label>
                                <input id="tipodoc" name="tipodoc" type="text" class="form-control" maxlength="2" value="<?= e($filters['tipodoc'] ?? '') ?>" placeholder="NE, PE">
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-search me-1"></i>Buscar
                            </button>
                            <a href="<?= e(url('/almacen/kardex')) ?>" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                            </a>
                            <a href="#" class="btn btn-outline-success px-4">
                                <i class="bi bi-file-pdf me-1"></i>Exportar PDF
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- Status Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-2">
                                <small class="text-muted d-block text-uppercase fw-semibold">Filtros activos</small>
                                <strong>Año <?= e($filters['ano'] ?? date('Y')) ?></strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-2">
                                <small class="text-muted d-block text-uppercase fw-semibold">Coincidencias</small>
                                <strong><?= count($kardexRows ?? []) ?></strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-2">
                                <small class="text-muted d-block text-uppercase fw-semibold">Estado de la pantalla</small>
                                <strong class="text-success">Consulta activa</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div>
                        <div class="mb-3">
                            <h3 class="h6 fw-semibold mb-1">Kardex de productos</h3>
                            <p class="text-muted small mb-0">Movimientos de ingresos, salidas y saldos por artículo</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Número</th>
                                        <th>Artículo</th>
                                        <th>Descripción</th>
                                        <th class="text-end">Ingresos</th>
                                        <th class="text-end">Salidas</th>
                                        <th class="text-end">Saldo</th>
                                        <th class="text-end">P. Prom.</th>
                                        <th class="text-end">Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($kardexRows)): ?>
                                        <?php foreach ($kardexRows as $index => $row): ?>
                                            <tr>
                                                <td><small><?= e($row['fecha']) ?></small></td>
                                                <td><span class="badge bg-info-subtle text-info"><?= e($row['tipodoc']) ?></span></td>
                                                <td><?= e($row['numero']) ?></td>
                                                <td><?= e($row['articulo']) ?></td>
                                                <td><small><?= e(substr($row['descripcion'], 0, 40)) ?></small></td>
                                                <td class="text-end"><small><?= number_format($row['ingresos'], 2) ?></small></td>
                                                <td class="text-end"><small><?= number_format($row['salidas'], 2) ?></small></td>
                                                <td class="text-end"><strong><?= number_format($row['saldo'], 2) ?></strong></td>
                                                <td class="text-end"><small>S/. <?= number_format($row['precio_promedio'], 2) ?></small></td>
                                                <td class="text-end"><strong>S/. <?= number_format($row['total'], 2) ?></strong></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="#" class="btn btn-outline-primary" title="Ver detalles">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-outline-secondary" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11" class="text-center py-5">
                                                <div>
                                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                                    <h5 class="text-muted mt-3">No hay resultados</h5>
                                                    <p class="text-muted small">Prueba cambiando los filtros.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Module Shortcuts -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">Atajos del módulo</h5>
                </div>
                <div class="card-body p-3 vstack gap-2">
                    <?php if (!empty($moduleActions)): ?>
                        <?php foreach ($moduleActions as $action): ?>
                            <a href="#" class="btn btn-sm btn-outline-<?= e($action['color'] ?? 'secondary') ?> text-start d-flex align-items-center gap-2">
                                <i class="bi <?= e($action['icon']) ?>"></i>
                                <span>
                                    <div class="fw-semibold small"><?= e($action['title']) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= e($action['description']) ?></div>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Module Sections -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">Secciones del módulo</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Pantallas y utilidades asociadas al kardex.</p>

                    <?php if (!empty($classicBlocks)): ?>
                        <?php foreach ($classicBlocks as $block): ?>
                            <div class="mb-3">
                                <h6 class="fw-semibold text-uppercase text-muted small mb-2"><?= e($block['title']) ?></h6>
                                <?php if (!empty($block['items'])): ?>
                                    <ul class="list-unstyled small">
                                        <?php foreach ($block['items'] as $item): ?>
                                            <li class="mb-1">
                                                <code class="bg-light px-2 py-1 rounded d-inline-block"><?= e($item) ?></code>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
