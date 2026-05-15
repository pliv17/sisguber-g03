<?php
/** @var array $filters */
/** @var array $rows */
?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">REPORTE DE ÓRDENES DE COMPRA</div>
    <div class="card-body">
        <form method="get" class="row g-3 mb-3">
            <div class="col-auto">
                <label class="form-label">Periodo</label>
                <select name="ano" class="form-select">
                    <?php for ($y = (int) date('Y'); $y >= 2012; $y--): ?>
                        <option value="<?= e((string) $y) ?>" <?= (($filters['ano'] ?? '') == (string) $y) ? 'selected' : '' ?>><?= e((string) $y) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label">Fecha inicio</label>
                <input type="text" name="fechainicio" value="<?= e($filters['fechainicio'] ?? '') ?>" class="form-control" placeholder="dd/mm/yyyy">
            </div>
            <div class="col-auto">
                <label class="form-label">Fecha fin</label>
                <input type="text" name="fechafin" value="<?= e($filters['fechafin'] ?? '') ?>" class="form-control" placeholder="dd/mm/yyyy">
            </div>
            <div class="col-auto">
                <label class="form-label">Proveedor</label>
                <input type="text" name="proveedor" value="<?= e($filters['proveedor'] ?? '') ?>" class="form-control" placeholder="RUC o nombre">
            </div>
            <div class="col-auto align-self-end">
                <button class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>N. Orden</th>
                        <th>Proveedor</th>
                        <th>Importe</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay resultados</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; foreach ($rows as $row): ?>
                            <tr>
                                <td><?= e((string) $i) ?></td>
                                <td><?= e($row['norden'] ?? '') ?></td>
                                <td><?= e($row['proveedor'] ?? '') ?></td>
                                <td class="text-end">S/ <?= number_format((float) ($row['total'] ?? 0), 2, '.', ',') ?></td>
                                <td class="text-center"><?= e($row['fecha'] ?? '') ?></td>
                                <td class="text-center"><?= ((string) ($row['estado'] ?? '')) === '1' ? 'Normal' : 'Anulada' ?></td>
                            </tr>
                        <?php $i++; endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
