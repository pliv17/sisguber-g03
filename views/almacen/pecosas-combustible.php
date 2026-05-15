<?php
/**
 * Vista principal del módulo PECOSA combustible.
 *
 * Variables disponibles:
 * @var string $moduleTitle
 * @var string $moduleLead
 * @var array<string, string> $filters
 * @var array<int, array{label:string, value:string}> $moduleStats
 * @var array<int, array{title:string, description:string, icon:string, color:string}> $moduleActions
 * @var array<int, array{norden:string, almacen:string, oficina:string, proveedor:string, combustible:string, cantidad:float, total:float, fecha:string, estado:string, ano:string}> $pecosaCombustibleRows
 * @var array<int, array{title:string, items: array<int, string>}> $classicBlocks
 */
?>

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 py-1 px-1">
            <div>
                <div class="text-uppercase text-primary fw-semibold small mb-1">Almacén</div>
                <h1 class="h3 fw-bold mb-1">PECOSA combustible</h1>
                <p class="text-muted mb-0"><?= e($moduleLead ?? 'Consulta, registro y mantenimiento de PECOSA de combustible.') ?></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach (($moduleStats ?? []) as $stat): ?>
                    <div class="border rounded-4 px-3 py-2 bg-white shadow-sm">
                        <div class="text-muted small text-uppercase"><?= e($stat['label'] ?? '') ?></div>
                        <div class="fw-semibold"><?= e($stat['value'] ?? '') ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-9">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h2 class="h5 fw-semibold mb-1">Consulta de PECOSA combustible</h2>
                        <p class="text-muted small mb-0">Filtros, resultados y acciones para salidas de combustible.</p>
                    </div>
                    <span class="badge text-bg-primary-subtle text-primary border border-primary-subtle">Endpoint /almacen/pecosas-combustible</span>
                </div>
            </div>

            <div class="card-body p-4">
                <form method="get" action="<?= e(url('/almacen/pecosas-combustible')) ?>" class="vstack gap-3">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="ano" class="form-label fw-semibold small text-uppercase text-muted">Período</label>
                            <select id="ano" name="ano" class="form-select">
                                <?php foreach ($availableYears ?? range((int) date('Y'), 2012, -1) as $year): ?>
                                    <option value="<?= e($year) ?>" <?= (($filters['ano'] ?? '') == (string) $year) ? 'selected' : '' ?>><?= e($year) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="almacen" class="form-label fw-semibold small text-uppercase text-muted">Almacén</label>
                            <input id="almacen" name="almacen" type="text" class="form-control" maxlength="3" value="<?= e($filters['almacen'] ?? '') ?>" placeholder="001">
                        </div>
                        <div class="col-md-4">
                            <label for="oficina" class="form-label fw-semibold small text-uppercase text-muted">Oficina solicitante</label>
                            <input id="oficina" name="oficina" type="text" class="form-control" maxlength="100" value="<?= e($filters['oficina'] ?? '') ?>" placeholder="Oficina">
                        </div>
                        <div class="col-md-2">
                            <label for="norden" class="form-label fw-semibold small text-uppercase text-muted">N. PECOSA</label>
                            <input id="norden" name="norden" type="text" class="form-control" maxlength="15" value="<?= e($filters['norden'] ?? '') ?>" placeholder="00000000">
                        </div>
                        <div class="col-md-2">
                            <label for="estado" class="form-label fw-semibold small text-uppercase text-muted">Estado</label>
                            <select id="estado" name="estado" class="form-select">
                                <option value="" <?= (($filters['estado'] ?? '') === '') ? 'selected' : '' ?>>Todos</option>
                                <option value="1" <?= (($filters['estado'] ?? '') === '1') ? 'selected' : '' ?>>Normal</option>
                                <option value="2" <?= (($filters['estado'] ?? '') === '2') ? 'selected' : '' ?>>Anulada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="fechaini" class="form-label fw-semibold small text-uppercase text-muted">Fecha inicio</label>
                            <input id="fechaini" name="fechaini" type="text" class="form-control" value="<?= e($filters['fechaini'] ?? '') ?>" placeholder="dd/mm/aaaa">
                        </div>
                        <div class="col-md-3">
                            <label for="fechafin" class="form-label fw-semibold small text-uppercase text-muted">Fecha fin</label>
                            <input id="fechafin" name="fechafin" type="text" class="form-control" value="<?= e($filters['fechafin'] ?? '') ?>" placeholder="dd/mm/aaaa">
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-search me-1"></i>Buscar
                        </button>
                        <a href="<?= e(url('/almacen/pecosas-combustible')) ?>" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                        </a>
                        <a href="#" class="btn btn-success px-4">
                            <i class="bi bi-plus-circle me-1"></i>Nueva PECOSA combustible
                        </a>
                    </div>
                </form>

                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light h-100">
                            <div class="text-muted small text-uppercase fw-semibold">Filtros activos</div>
                            <div class="fw-semibold mt-1"><?= e(implode(' · ', array_filter([
                                ($filters['ano'] ?? '') !== '' ? 'Año ' . ($filters['ano'] ?? '') : '',
                                ($filters['almacen'] ?? '') !== '' ? 'Almacén ' . ($filters['almacen'] ?? '') : '',
                                ($filters['oficina'] ?? '') !== '' ? ($filters['oficina'] ?? '') : '',
                                ($filters['norden'] ?? '') !== '' ? 'N. ' . ($filters['norden'] ?? '') : '',
                            ])) ?: 'Sin filtros adicionales') ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light h-100">
                            <div class="text-muted small text-uppercase fw-semibold">Coincidencias</div>
                            <div class="display-6 fw-bold mb-0"><?= e(count($pecosaCombustibleRows ?? [])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light h-100">
                            <div class="text-muted small text-uppercase fw-semibold">Estado de la pantalla</div>
                            <div class="fw-semibold mt-1 text-success">Consulta activa</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">Relación de PECOSA combustible</h3>
                        <span class="text-muted small">Listado principal de salidas de combustible</span>
                    </div>

                    <div class="table-responsive rounded-4 border shadow-sm">
                        <table class="table align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width: 8%">Item</th>
                                    <th style="width: 12%">N. PECOSA</th>
                                    <th style="width: 10%">Almacén</th>
                                    <th>Oficina</th>
                                    <th>Proveedor</th>
                                    <th style="width: 12%">Combustible</th>
                                    <th style="width: 10%">Cantidad</th>
                                    <th style="width: 12%">Importe</th>
                                    <th style="width: 12%">Fecha</th>
                                    <th style="width: 10%">Estado</th>
                                    <th style="width: 14%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pecosaCombustibleRows)): ?>
                                    <?php foreach ($pecosaCombustibleRows as $index => $row): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= e($index + 1) ?></td>
                                            <td><?= e($row['norden']) ?></td>
                                            <td><?= e($row['almacen']) ?></td>
                                            <td><?= e($row['oficina']) ?></td>
                                            <td><?= e($row['proveedor']) ?></td>
                                            <td><?= e($row['combustible']) ?></td>
                                            <td class="text-end"><?= e(number_format((float) $row['cantidad'], 2, '.', ',')) ?></td>
                                            <td class="text-end">S/ <?= e(number_format((float) $row['total'], 2, '.', ',')) ?></td>
                                            <td><?= e($row['fecha']) ?></td>
                                            <td>
                                                <?php if (($row['estado'] ?? '') === 'Normal'): ?>
                                                    <span class="badge text-bg-success">Normal</span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-danger">Anulada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Acciones PECOSA combustible">
                                                    <a class="btn btn-outline-primary" href="#" title="Modificar"><i class="bi bi-pencil"></i></a>
                                                    <a class="btn btn-outline-secondary" href="#" title="Imprimir"><i class="bi bi-printer"></i></a>
                                                    <a class="btn btn-outline-danger" href="#" title="Anular"><i class="bi bi-x-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center py-5">
                                            <div class="text-muted mb-2"><i class="bi bi-search fs-1"></i></div>
                                            <h4 class="h6 fw-semibold mb-1">No hay resultados</h4>
                                            <p class="text-muted mb-0">Prueba cambiando los filtros.</p>
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

    <div class="col-12 col-xl-3">
        <div class="vstack gap-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-3">Atajos del módulo</h2>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary"><i class="bi bi-file-earmark-plus me-1"></i>Nueva PECOSA combustible</a>
                        <a href="#" class="btn btn-outline-success"><i class="bi bi-pencil-square me-1"></i>Modificar comprobante</a>
                        <a href="#" class="btn btn-outline-warning"><i class="bi bi-patch-check me-1"></i>Validar orden</a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-3">Secciones del módulo</h2>
                    <div class="small text-muted mb-3">Pantallas y utilidades asociadas a la PECOSA combustible.</div>
                    <ul class="list-unstyled small mb-0">
                        <?php foreach (($classicBlocks ?? []) as $block): ?>
                            <li class="mb-3">
                                <div class="fw-semibold mb-1"><?= e($block['title'] ?? '') ?></div>
                                <div class="text-muted"><?= e(implode(', ', $block['items'] ?? [])) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
