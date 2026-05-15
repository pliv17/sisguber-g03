<?php
/**
 * _layout.php - SisGuber G03
 * Plantilla base del sistema. Incluye navbar + sidebar.
 * Todas las páginas protegidas hacen:
 *   $pageTitle = "Mi Página";
 *   $activeMenu = "proveedores";
 *   require_once __DIR__ . '/../layout/_layout.php';
 *   // ... contenido ...
 *   require_once __DIR__ . '/../layout/_layout_end.php';
 */
if (!defined('APP_ACCESS')) { header('Location: ../auth/login.php'); exit; }

$userName  = $_SESSION['nombres']  ?? 'Usuario';
$userLast  = $_SESSION['apellidos'] ?? '';
$userLevel = $_SESSION['nivel']    ?? 'us';
$userOffice= $_SESSION['oficina']  ?? '';
$initials  = strtoupper(substr($userName, 0, 1) . substr($userLast, 0, 1));
$pageTitle = $pageTitle ?? 'SisGuber';
$activeMenu= $activeMenu ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?> — SisGuber</title>
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../layout/layout.css">
  <?php if (!empty($extraCSS)): foreach ($extraCSS as $css): ?>
  <link rel="stylesheet" href="<?php echo $css; ?>">
  <?php endforeach; endif; ?>
</head>
<body>

<!-- OVERLAY MOBILE -->
<div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:99"
     class=""></div>

<!-- =================== NAVBAR =================== -->
<nav class="navbar">
  <a href="../layout/index2.php" class="navbar-brand">
    <div class="brand-icon">🏛️</div>
    SisGuber
  </a>
  <button class="navbar-toggle" id="sidebarToggle" title="Colapsar menú">☰</button>

  <div class="navbar-spacer"></div>

  <div class="navbar-right">
    <!-- Acceso rápido al módulo principal -->
    <a href="../layout/index2.php" class="btn btn-secondary btn-sm"
       style="font-size:.78rem">🏠 Inicio</a>

    <!-- Menú de usuario -->
    <div class="navbar-user" id="userMenuBtn">
      <div class="user-avatar"><?php echo $initials; ?></div>
      <div class="user-info">
        <div class="user-name"><?php echo htmlspecialchars($userName . ' ' . $userLast); ?></div>
        <div class="user-role"><?php echo $userLevel === 'ad' ? 'Administrador' : 'Usuario'; ?></div>
      </div>
      <span style="font-size:.7rem;color:#94a3b8;margin-left:.25rem">▾</span>
    </div>

    <!-- Dropdown -->
    <div class="user-dropdown" id="userDropdown">
      <div class="dropdown-header">
        <div class="dh-name"><?php echo htmlspecialchars($userName . ' ' . $userLast); ?></div>
        <div class="dh-email"><?php echo htmlspecialchars($userOffice); ?></div>
      </div>
      <a href="../auth/cambiar_contrasena.php">🔑 Cambiar contraseña</a>
      <?php if ($userLevel === 'ad'): ?>
      <a href="../usuarios/usuarios.php">👤 Gestión de usuarios</a>
      <?php endif; ?>
      <div class="dropdown-divider"></div>
      <a href="../auth/logout.php" class="logout-btn">🚪 Cerrar sesión</a>
    </div>
  </div>
</nav>

<!-- =================== SIDEBAR =================== -->
<aside class="sidebar" id="sidebar">
  <nav class="sidebar-nav">

    <div class="nav-section-label">Principal</div>
    <a href="../layout/index2.php"
       class="nav-item <?php echo $activeMenu === 'inicio' ? 'active' : ''; ?>">
      <span class="nav-icon">🏠</span>
      <span class="nav-label">Inicio</span>
    </a>

    <!-- ARCHIVOS MAESTROS -->
    <div class="nav-section-label">Archivos Maestros</div>
    <div class="nav-item <?php echo strpos($activeMenu, 'maestros') !== false ? 'open' : ''; ?>"
         data-submenu="sub-maestros">
      <span class="nav-icon">📁</span>
      <span class="nav-label">Archivos Maestros</span>
      <span class="nav-arrow">›</span>
    </div>
    <div class="nav-submenu <?php echo strpos($activeMenu, 'maestros') !== false ? 'open' : ''; ?>"
         id="sub-maestros">
      <a href="../enlaces/almacen.php"    class="nav-item <?php echo $activeMenu==='almacen'    ? 'active':'' ?>"><span class="nav-icon">🏭</span><span class="nav-label">Almacén</span></a>
      <a href="../enlaces/medida.php"     class="nav-item <?php echo $activeMenu==='medida'     ? 'active':'' ?>"><span class="nav-icon">📏</span><span class="nav-label">Unidades Medida</span></a>
      <a href="../enlaces/rubro.php"      class="nav-item <?php echo $activeMenu==='rubro'      ? 'active':'' ?>"><span class="nav-icon">🏷️</span><span class="nav-label">Rubros</span></a>
      <a href="../enlaces/proveedores.php"class="nav-item <?php echo $activeMenu==='proveedores'? 'active':'' ?>"><span class="nav-icon">🏢</span><span class="nav-label">Proveedores</span></a>
      <a href="../enlaces/productos.php"  class="nav-item <?php echo $activeMenu==='productos'  ? 'active':'' ?>"><span class="nav-icon">📦</span><span class="nav-label">Catálogo Bienes</span></a>
      <a href="../enlaces/servicios.php"  class="nav-item <?php echo $activeMenu==='servicios'  ? 'active':'' ?>"><span class="nav-icon">🔧</span><span class="nav-label">Catálogo Servicios</span></a>
      <a href="../enlaces/oficinas.php"   class="nav-item <?php echo $activeMenu==='oficinas'   ? 'active':'' ?>"><span class="nav-icon">🏬</span><span class="nav-label">Oficinas</span></a>
      <a href="../enlaces/fuente.php"     class="nav-item <?php echo $activeMenu==='fuente'     ? 'active':'' ?>"><span class="nav-icon">💰</span><span class="nav-label">Fuentes</span></a>
      <a href="../enlaces/metas.php"      class="nav-item <?php echo $activeMenu==='metas'      ? 'active':'' ?>"><span class="nav-icon">🎯</span><span class="nav-label">Metas</span></a>
      <a href="../enlaces/partidas.php"   class="nav-item <?php echo $activeMenu==='partidas'   ? 'active':'' ?>"><span class="nav-icon">📑</span><span class="nav-label">Partidas</span></a>
    </div>

    <!-- LOGÍSTICA -->
    <div class="nav-section-label">Logística</div>
    <div class="nav-item <?php echo strpos($activeMenu,'logistica')!==false ? 'open':'' ?>"
         data-submenu="sub-logistica">
      <span class="nav-icon">🚚</span>
      <span class="nav-label">Procesos Logística</span>
      <span class="nav-arrow">›</span>
    </div>
    <div class="nav-submenu <?php echo strpos($activeMenu,'logistica')!==false ? 'open':'' ?>"
         id="sub-logistica">
      <a href="../orden_compra/ordencompra.php"    class="nav-item <?php echo $activeMenu==='oc'?'active':'' ?>"><span class="nav-icon">🛒</span><span class="nav-label">Órdenes de Compra</span></a>
      <a href="../orden_servicio/ordenservicio.php"class="nav-item <?php echo $activeMenu==='os'?'active':'' ?>"><span class="nav-icon">📝</span><span class="nav-label">Órdenes de Servicio</span></a>
    </div>

    <!-- ALMACÉN -->
    <div class="nav-section-label">Almacén</div>
    <div class="nav-item <?php echo strpos($activeMenu,'almacen_proc')!==false ? 'open':'' ?>"
         data-submenu="sub-almacen">
      <span class="nav-icon">🏪</span>
      <span class="nav-label">Procesos Almacén</span>
      <span class="nav-arrow">›</span>
    </div>
    <div class="nav-submenu <?php echo strpos($activeMenu,'almacen_proc')!==false ? 'open':'' ?>"
         id="sub-almacen">
      <a href="../nota_entrada/notaentrada.php"  class="nav-item <?php echo $activeMenu==='nea'?'active':'' ?>"><span class="nav-icon">📥</span><span class="nav-label">Notas de Entrada</span></a>
      <a href="../pecosa/pecosa.php"             class="nav-item <?php echo $activeMenu==='pecosa'?'active':'' ?>"><span class="nav-icon">📤</span><span class="nav-label">PECOSA</span></a>
      <a href="../procesa_stock/stock.php"       class="nav-item <?php echo $activeMenu==='stock'?'active':'' ?>"><span class="nav-icon">📊</span><span class="nav-label">Actualizar Stock</span></a>
      <a href="../procesa_stock/kardex.php"      class="nav-item <?php echo $activeMenu==='kardex'?'active':'' ?>"><span class="nav-icon">📋</span><span class="nav-label">Kardex</span></a>
    </div>

    <!-- ADMINISTRACIÓN (solo admin) -->
    <?php if ($userLevel === 'ad'): ?>
    <div class="nav-section-label">Administración</div>
    <a href="../usuarios/usuarios.php"
       class="nav-item <?php echo $activeMenu==='usuarios' ? 'active':'' ?>">
      <span class="nav-icon">👥</span>
      <span class="nav-label">Usuarios</span>
    </a>
    <?php endif; ?>

  </nav>

  <div class="sidebar-footer">
    <div class="version">SisGuber v2.0 — G03</div>
  </div>
</aside>

<!-- =================== CONTENIDO =================== -->
<main class="main-content" id="mainContent">