<?php
/**
 * Vista placeholder para módulos pendientes de lógica de negocio.
 *
 * @var string $stubHeading
 * @var string $stubLead
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-layers fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h1 class="h3 fw-bold mb-2"><?= e($stubHeading ?? 'Módulo') ?></h1>
                        <p class="text-muted mb-0"><?= e($stubLead ?? 'Pantalla del sistema de abastecimiento.') ?></p>
                    </div>
                </div>
                <span class="badge bg-secondary">En construcción</span>
                <p class="small text-muted mt-3 mb-0">
                    Conecta aquí el servicio, repositorio y formularios siguiendo el patrón descrito en
                    <code>routes/web.php</code>.
                </p>
            </div>
        </div>
    </div>
</div>
