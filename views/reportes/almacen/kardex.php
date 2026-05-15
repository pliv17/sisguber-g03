<?php
/** @var array $filters */
/** @var array $rows */
?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">REPORTE DE KARDEX DE PRODUCTOS</div>
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
                <label class="form-label">Artículo</label>
                <input type="text" name="articulo" value="<?= e($filters['articulo'] ?? '') ?>" class="form-control" placeholder="Código">
            </div>
            <div class="col-auto">
                <label class="form-label">Fecha inicio</label>
                <input type="text" name="fechainicio" value="<?= e($filters['fechainicio'] ?? '') ?>" class="form-control" placeholder="dd/mm/yyyy">
            </div>
            <div class="col-auto">
                <label class="form-label">Fecha fin</label>
                <input type="text" name="fechafin" value="<?= e($filters['fechafin'] ?? '') ?>" class="form-control" placeholder="dd/mm/yyyy">
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
                        <th>Num.Doc</th>
                        <th>Tipo Doc.</th>
                        <th>Fecha</th>
                        <th>Código</th>
                        <th>Artículo</th>
                        <th>U.Medida</th>
                        <th>Ingresos</th>
                        <th>Salidas</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">No hay resultados</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1; $saldoAcum = 0; foreach ($rows as $row): ?>
                            <?php $saldoAcum += ((float) ($row['ingresos'] ?? 0) - (float) ($row['salidas'] ?? 0)); ?>
                            <tr>
                                <td><?= e((string) $i) ?></td>
                                <td><?= e($row['numdoc'] ?? '') ?></td>
                                <td><?= e($row['tipodoc'] ?? '') ?></td>
                                <td class="text-center"><?= e($row['fecha'] ?? '') ?></td>
                                <td><?= e($row['codigo'] ?? '') ?></td>
                                <td><?= e($row['nombre'] ?? '') ?></td>
                                <td class="text-center"><?= e($row['medida'] ?? '') ?></td>
                                <td class="text-end"><?= number_format((float) ($row['ingresos'] ?? 0), 2, '.', ',') ?></td>
                                <td class="text-end"><?= number_format((float) ($row['salidas'] ?? 0), 2, '.', ',') ?></td>
                                <td class="text-end"><?= number_format($saldoAcum, 2, '.', ',') ?></td>
                            </tr>
                        <?php $i++; endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
