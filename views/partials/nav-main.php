<?php

declare(strict_types=1);

/**
 * Barra de navegación principal (menú).
 * Usa helpers: request_path(), nav_active(), nav_dd_active().
 */
?>
<ul class="navbar-nav me-auto mb-2 mb-lg-0">

    <li class="nav-item">
        <a class="nav-link <?= e(nav_active('/')) ?>" <?= nav_is_active_path('/') ? 'aria-current="page"' : '' ?> href="<?= e(url('/')) ?>">
            <i class="bi bi-house me-1"></i>Inicio
        </a>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?= e(nav_dd_active(['/maestros'])) ?>"
           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-card-list me-1"></i>Maestros
        </a>
        <ul class="dropdown-menu dropdown-menu-lg-start">
            <li><h6 class="dropdown-header">Almacén y estructura</h6></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/almacenes')) ?>" <?= nav_is_active_path('/maestros/almacenes') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/almacenes')) ?>">Códigos de almacén</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/unidades-medida')) ?>" <?= nav_is_active_path('/maestros/unidades-medida') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/unidades-medida')) ?>">Unidades de medida</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/oficinas')) ?>" <?= nav_is_active_path('/maestros/oficinas') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/oficinas')) ?>">Códigos de oficinas</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><h6 class="dropdown-header">Proveedores</h6></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/rubros-proveedor')) ?>" <?= nav_is_active_path('/maestros/rubros-proveedor') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/rubros-proveedor')) ?>">Rubro del proveedor</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/proveedores')) ?>" <?= nav_is_active_path('/maestros/proveedores') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/proveedores')) ?>">Proveedores de bienes/servicios</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><h6 class="dropdown-header">Catálogo</h6></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/catalogo/bienes')) ?>" <?= nav_is_active_path('/maestros/catalogo/bienes') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/catalogo/bienes')) ?>">Catálogo de bienes</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/catalogo/servicios')) ?>" <?= nav_is_active_path('/maestros/catalogo/servicios') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/catalogo/servicios')) ?>">Catálogo de servicios</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><h6 class="dropdown-header">Presupuesto</h6></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/fuentes-financiamiento')) ?>" <?= nav_is_active_path('/maestros/fuentes-financiamiento') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/fuentes-financiamiento')) ?>">Fuente de financiamiento / rubros</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/metas')) ?>" <?= nav_is_active_path('/maestros/metas') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/metas')) ?>">Metas presupuestales</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/maestros/partidas')) ?>" <?= nav_is_active_path('/maestros/partidas') ? 'aria-current="page"' : '' ?> href="<?= e(url('/maestros/partidas')) ?>">Partidas presupuestales</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?= e(nav_dd_active(['/ordenes', '/logistica'])) ?>"
           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-truck me-1"></i>Logística
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item <?= e(nav_active('/ordenes/compra')) ?>" <?= nav_is_active_path('/ordenes/compra') ? 'aria-current="page"' : '' ?> href="<?= e(url('/ordenes/compra')) ?>">Órdenes de compra</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/ordenes/servicio')) ?>" <?= nav_is_active_path('/ordenes/servicio') ? 'aria-current="page"' : '' ?> href="<?= e(url('/ordenes/servicio')) ?>">Órdenes de servicio</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/logistica/cuadro-comparativo')) ?>" <?= nav_is_active_path('/logistica/cuadro-comparativo') ? 'aria-current="page"' : '' ?> href="<?= e(url('/logistica/cuadro-comparativo')) ?>">Cuadro comparativo</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/logistica/cuadro-necesidades')) ?>" <?= nav_is_active_path('/logistica/cuadro-necesidades') ? 'aria-current="page"' : '' ?> href="<?= e(url('/logistica/cuadro-necesidades')) ?>">Cuadro de necesidades</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?= e(nav_dd_active(['/almacen'])) ?>"
           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-archive me-1"></i>Almacén
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item <?= e(nav_active('/almacen/notas-entrada')) ?>" <?= nav_is_active_path('/almacen/notas-entrada') ? 'aria-current="page"' : '' ?> href="<?= e(url('/almacen/notas-entrada')) ?>">Nota de entrada (NEA)</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/almacen/pecosas')) ?>" <?= nav_is_active_path('/almacen/pecosas') ? 'aria-current="page"' : '' ?> href="<?= e(url('/almacen/pecosas')) ?>">PECOSA</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/almacen/pecosas-combustible')) ?>" <?= nav_is_active_path('/almacen/pecosas-combustible') ? 'aria-current="page"' : '' ?> href="<?= e(url('/almacen/pecosas-combustible')) ?>">PECOSA combustible</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/almacen/vales-combustible')) ?>" <?= nav_is_active_path('/almacen/vales-combustible') ? 'aria-current="page"' : '' ?> href="<?= e(url('/almacen/vales-combustible')) ?>">Vales de combustible</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item <?= e(nav_active('/almacen/stock')) ?>" <?= nav_is_active_path('/almacen/stock') ? 'aria-current="page"' : '' ?> href="<?= e(url('/almacen/stock')) ?>">Stock de productos</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/almacen/kardex')) ?>" <?= nav_is_active_path('/almacen/kardex') ? 'aria-current="page"' : '' ?> href="<?= e(url('/almacen/kardex')) ?>">Kardex de productos</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?= e(nav_dd_active(['/reportes'])) ?>"
           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-file-earmark-bar-graph me-1"></i>Reportes
        </a>
        <ul class="dropdown-menu dropdown-menu-lg-start">
            <li><a class="dropdown-item fw-semibold <?= e(nav_active('/reportes')) ?>" <?= nav_is_active_path('/reportes') ? 'aria-current="page"' : '' ?> href="<?= e(url('/reportes')) ?>">Índice de reportes</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><h6 class="dropdown-header">Logística</h6></li>
            <li><a class="dropdown-item <?= e(nav_active('/reportes/logistica/ordenes-compra')) ?>" <?= nav_is_active_path('/reportes/logistica/ordenes-compra') ? 'aria-current="page"' : '' ?> href="<?= e(url('/reportes/logistica/ordenes-compra')) ?>">Órdenes de compra</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/reportes/logistica/ordenes-servicio')) ?>" <?= nav_is_active_path('/reportes/logistica/ordenes-servicio') ? 'aria-current="page"' : '' ?> href="<?= e(url('/reportes/logistica/ordenes-servicio')) ?>">Órdenes de servicio</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><h6 class="dropdown-header">Almacén</h6></li>
            <li><a class="dropdown-item <?= e(nav_active('/reportes/almacen/notas-entrada')) ?>" <?= nav_is_active_path('/reportes/almacen/notas-entrada') ? 'aria-current="page"' : '' ?> href="<?= e(url('/reportes/almacen/notas-entrada')) ?>">Notas de entrada (NEA)</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/reportes/almacen/pecosas')) ?>" <?= nav_is_active_path('/reportes/almacen/pecosas') ? 'aria-current="page"' : '' ?> href="<?= e(url('/reportes/almacen/pecosas')) ?>">PECOSA</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/reportes/almacen/pecosas-combustible')) ?>" <?= nav_is_active_path('/reportes/almacen/pecosas-combustible') ? 'aria-current="page"' : '' ?> href="<?= e(url('/reportes/almacen/pecosas-combustible')) ?>">PECOSA combustible</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/reportes/almacen/stock-fisico')) ?>" <?= nav_is_active_path('/reportes/almacen/stock-fisico') ? 'aria-current="page"' : '' ?> href="<?= e(url('/reportes/almacen/stock-fisico')) ?>">Stock físico por producto</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/reportes/almacen/kardex')) ?>" <?= nav_is_active_path('/reportes/almacen/kardex') ? 'aria-current="page"' : '' ?> href="<?= e(url('/reportes/almacen/kardex')) ?>">Kardex diario por producto</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?= e(nav_dd_active(['/utilidades'])) ?>"
           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-tools me-1"></i>Utilidades
        </a>
        <ul class="dropdown-menu dropdown-menu-lg-start">
            <li><h6 class="dropdown-header">Copia de seguridad</h6></li>
            <li><a class="dropdown-item <?= e(nav_active('/utilidades/respaldo')) ?>" <?= nav_is_active_path('/utilidades/respaldo') ? 'aria-current="page"' : '' ?> href="<?= e(url('/utilidades/respaldo')) ?>">Respaldo — inicio</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/utilidades/respaldo/crear')) ?>" <?= nav_is_active_path('/utilidades/respaldo/crear') ? 'aria-current="page"' : '' ?> href="<?= e(url('/utilidades/respaldo/crear')) ?>">Hacer copia</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/utilidades/respaldo/restaurar')) ?>" <?= nav_is_active_path('/utilidades/respaldo/restaurar') ? 'aria-current="page"' : '' ?> href="<?= e(url('/utilidades/respaldo/restaurar')) ?>">Restaurar copia</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item <?= e(nav_active('/utilidades/neas-manuales')) ?>" <?= nav_is_active_path('/utilidades/neas-manuales') ? 'aria-current="page"' : '' ?> href="<?= e(url('/utilidades/neas-manuales')) ?>">NEA manuales</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/utilidades/pecosas-manuales')) ?>" <?= nav_is_active_path('/utilidades/pecosas-manuales') ? 'aria-current="page"' : '' ?> href="<?= e(url('/utilidades/pecosas-manuales')) ?>">PECOSA manuales</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/utilidades/claves')) ?>" <?= nav_is_active_path('/utilidades/claves') ? 'aria-current="page"' : '' ?> href="<?= e(url('/utilidades/claves')) ?>">Mantenimiento de claves</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><h6 class="dropdown-header">Migración SIAF</h6></li>
            <li><a class="dropdown-item <?= e(nav_active('/utilidades/migracion-siaf/metas')) ?>" <?= nav_is_active_path('/utilidades/migracion-siaf/metas') ? 'aria-current="page"' : '' ?> href="<?= e(url('/utilidades/migracion-siaf/metas')) ?>">Metas presupuestales</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/utilidades/migracion-siaf/fuentes')) ?>" <?= nav_is_active_path('/utilidades/migracion-siaf/fuentes') ? 'aria-current="page"' : '' ?> href="<?= e(url('/utilidades/migracion-siaf/fuentes')) ?>">Fuentes de financiamiento</a></li>
            <li><a class="dropdown-item <?= e(nav_active('/utilidades/migracion-siaf/partidas')) ?>" <?= nav_is_active_path('/utilidades/migracion-siaf/partidas') ? 'aria-current="page"' : '' ?> href="<?= e(url('/utilidades/migracion-siaf/partidas')) ?>">Partidas presupuestales</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?= e(nav_dd_active(['/acerca-de'])) ?>"
           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-info-circle me-1"></i>Acerca de
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item <?= e(nav_active('/acerca-de/creditos')) ?>" <?= nav_is_active_path('/acerca-de/creditos') ? 'aria-current="page"' : '' ?> href="<?= e(url('/acerca-de/creditos')) ?>">Créditos</a></li>
            <li>
                <a class="dropdown-item" href="<?= e(asset('manual/SisGuber_abaste.pdf')) ?>" target="_blank" rel="noopener">
                    Guía de utilización (PDF)
                </a>
            </li>
        </ul>
    </li>
</ul>

<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
    <li class="nav-item">
        <a class="nav-link <?= e(nav_active('/health')) ?>" <?= nav_is_active_path('/health') ? 'aria-current="page"' : '' ?> href="<?= e(url('/health')) ?>" target="_blank" title="Estado del sistema">
            <i class="bi bi-heart-pulse me-1"></i>Health
        </a>
    </li>
</ul>
