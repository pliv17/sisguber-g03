<?php
/**
 * Vista principal del módulo NEA.
 *
 * Variables disponibles:
 * @var string $moduleTitle
 * @var string $moduleLead
 * @var array<string, string> $filters
 * @var array<int, array{label:string, value:string}> $moduleStats
 * @var array<int, array{title:string, description:string, icon:string, color:string}> $moduleActions
 * @var array<int, array{norden:string, proveedor:string, total:float, fecha:string, estado:string, ano:string}> $neaRows
 * @var array<int, array{title:string, items: array<int, string>}> $classicBlocks
 */
?>

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 py-1 px-1">
            <div>
                <div class="text-uppercase text-primary fw-semibold small mb-1">Almacén</div>
                <h1 class="h3 fw-bold mb-1">Consulta de notas de entrada</h1>
                <p class="text-muted mb-0"><?= e($moduleLead ?? 'Consulta, registro y mantenimiento de notas de entrada del almacén.') ?></p>
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
                        <h2 class="h5 fw-semibold mb-1">Consulta de Notas de Entrada</h2>
                        <p class="text-muted small mb-0">Filtros y resultados de notas de entrada con una interfaz más limpia y moderna.</p>
                    </div>
                    <span class="badge text-bg-primary-subtle text-primary border border-primary-subtle">Endpoint /almacen/notas-entrada</span>
                </div>
            </div>

            <div class="card-body p-4">
                <form method="get" action="<?= e(url('/almacen/notas-entrada')) ?>" class="vstack gap-3">
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
                            <label for="proveedor" class="form-label fw-semibold small text-uppercase text-muted">RUC</label>
                            <input id="proveedor" name="proveedor" type="text" class="form-control" maxlength="11" value="<?= e($filters['proveedor'] ?? '') ?>" placeholder="Proveedor">
                        </div>
                        <div class="col-md-4">
                            <label for="nombre" class="form-label fw-semibold small text-uppercase text-muted">Proveedor</label>
                            <input id="nombre" name="nombre" type="text" class="form-control" maxlength="45" value="<?= e($filters['nombre'] ?? '') ?>" placeholder="Nombre o razón social">
                        </div>
                        <div class="col-md-2">
                            <label for="norden" class="form-label fw-semibold small text-uppercase text-muted">N. NEA</label>
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
                            <label for="fechainicio" class="form-label fw-semibold small text-uppercase text-muted">Fecha inicio</label>
                            <input id="fechainicio" name="fechainicio" type="text" class="form-control" value="<?= e($filters['fechainicio'] ?? '') ?>" placeholder="dd/mm/aaaa">
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
                        <a href="<?= e(url('/almacen/notas-entrada')) ?>" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                        </a>
                        <a href="#" class="btn btn-success px-4">
                            <i class="bi bi-plus-circle me-1"></i>Nueva NEA
                        </a>
                    </div>
                </form>

                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light h-100">
                            <div class="text-muted small text-uppercase fw-semibold">Filtros activos</div>
                            <div class="fw-semibold mt-1"><?= e(implode(' · ', array_filter([
                                ($filters['ano'] ?? '') !== '' ? 'Año ' . ($filters['ano'] ?? '') : '',
                                ($filters['proveedor'] ?? '') !== '' ? 'RUC ' . ($filters['proveedor'] ?? '') : '',
                                ($filters['nombre'] ?? '') !== '' ? ($filters['nombre'] ?? '') : '',
                                ($filters['norden'] ?? '') !== '' ? 'N. ' . ($filters['norden'] ?? '') : '',
                            ])) ?: 'Sin filtros adicionales') ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light h-100">
                            <div class="text-muted small text-uppercase fw-semibold">Coincidencias</div>
                            <div class="display-6 fw-bold mb-0"><?= e(count($neaRows ?? [])) ?></div>
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
                        <h3 class="h6 fw-bold mb-0">Relación de Notas de Entrada</h3>
                        <span class="text-muted small">Vista modernizada sobre la lógica heredada</span>
                    </div>

                    <div class="table-responsive rounded-4 border shadow-sm">
                        <table class="table align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width: 8%">Item</th>
                                    <th style="width: 12%">N. NEA</th>
                                    <th>Proveedor</th>
                                    <th style="width: 14%">Importe</th>
                                    <th style="width: 12%">Fecha</th>
                                    <th style="width: 10%">Estado</th>
                                    <th style="width: 14%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($neaRows)): ?>
                                    <?php foreach ($neaRows as $index => $row): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= e($index + 1) ?></td>
                                            <td><?= e($row['norden']) ?></td>
                                            <td><?= e($row['proveedor']) ?></td>
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
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Acciones NEA">
                                                    <a class="btn btn-outline-primary" href="#" title="Modificar"><i class="bi bi-pencil"></i></a>
                                                    <a class="btn btn-outline-secondary" href="#" title="Imprimir"><i class="bi bi-printer"></i></a>
                                                    <a class="btn btn-outline-danger" href="#" title="Anular"><i class="bi bi-x-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted mb-2"><i class="bi bi-search fs-1"></i></div>
                                            <h4 class="h6 fw-semibold mb-1">No hay resultados</h4>
                                            <p class="text-muted mb-0">Prueba cambiando los filtros o revisa la conexión a datos cuando se conecte el origen real.</p>
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
                        <a href="#" class="btn btn-outline-primary"><i class="bi bi-file-earmark-plus me-1"></i>Registrar NEA</a>
                        <a href="#" class="btn btn-outline-success"><i class="bi bi-box-seam me-1"></i>Ingresar productos</a>
                        <a href="#" class="btn btn-outline-warning"><i class="bi bi-receipt me-1"></i>Vincular OC / factura</a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-3">Secciones clásicas</h2>
                    <div class="small text-muted mb-3">Pantallas y utilidades asociadas a la NEA.</div>
                    <ul class="list-unstyled small mb-0">
                        <?php foreach (($classicBlocks ?? []) as $block): ?>
                            <li class="mb-3">
                                <div class="fw-semibold mb-1"><?= e($block['title'] ?? '') ?></div>
                                <div class="text-muted">
                                    <?= e(implode(', ', $block['items'] ?? [])) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
