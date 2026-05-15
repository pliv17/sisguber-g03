<?php
/**
 * index2.php - SisGuber G03
 * Página de inicio del sistema (dashboard).
 * Usa el nuevo layout con navbar + sidebar.
 */
define('APP_ACCESS', true);
define('BASE_URL', '..');
require_once __DIR__ . '/../auth/session_guard.php';
require_once __DIR__ . '/../conexion.php';

$pageTitle  = 'Inicio';
$activeMenu = 'inicio';

// Estadísticas rápidas para el dashboard
$stats = [];
$queries = [
  'proveedores' => "SELECT COUNT(*) as n FROM proveedores",
  'productos'   => "SELECT COUNT(*) as n FROM productos",
  'oc_pendientes'=> "SELECT COUNT(*) as n FROM ordenlinea WHERE estado='PE'",
  'nea_mes'     => "SELECT COUNT(*) as n FROM nota_entrada WHERE MONTH(fecha)=MONTH(NOW()) AND YEAR(fecha)=YEAR(NOW())",
];
foreach ($queries as $key => $sql) {
  $r = mysqli_query($conexion, $sql);
  $stats[$key] = $r ? (mysqli_fetch_assoc($r)['n'] ?? 0) : 0;
}

require_once __DIR__ . '/_layout.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
  <span>🏠</span> <span>Inicio</span>
</div>

<!-- Page header -->
<div class="page-header">
  <div class="page-title">
    <h1>Panel Principal</h1>
    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombres'] . ' ' . $_SESSION['apellidos']); ?></p>
  </div>
  <div>
    <span class="badge badge-gray"><?php echo date('d/m/Y H:i'); ?></span>
  </div>
</div>

<!-- Tarjetas de estadísticas -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem">

  <div class="card" style="padding:1.25rem">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">
      <span style="font-size:.8rem;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px">Proveedores</span>
      <span style="font-size:1.3rem">🏢</span>
    </div>
    <div style="font-size:2rem;font-weight:700;color:var(--primary)"><?php echo $stats['proveedores']; ?></div>
    <div style="font-size:.78rem;color:var(--text-3);margin-top:.25rem">Registrados</div>
  </div>

  <div class="card" style="padding:1.25rem">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">
      <span style="font-size:.8rem;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px">Productos</span>
      <span style="font-size:1.3rem">📦</span>
    </div>
    <div style="font-size:2rem;font-weight:700;color:var(--success)"><?php echo $stats['productos']; ?></div>
    <div style="font-size:.78rem;color:var(--text-3);margin-top:.25rem">En catálogo</div>
  </div>

  <div class="card" style="padding:1.25rem">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">
      <span style="font-size:.8rem;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px">OC Pendientes</span>
      <span style="font-size:1.3rem">🛒</span>
    </div>
    <div style="font-size:2rem;font-weight:700;color:var(--warning)"><?php echo $stats['oc_pendientes']; ?></div>
    <div style="font-size:.78rem;color:var(--text-3);margin-top:.25rem">Sin atender</div>
  </div>

  <div class="card" style="padding:1.25rem">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">
      <span style="font-size:.8rem;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px">NEA del Mes</span>
      <span style="font-size:1.3rem">📥</span>
    </div>
    <div style="font-size:2rem;font-weight:700;color:var(--info,#3b82f6)"><?php echo $stats['nea_mes']; ?></div>
    <div style="font-size:.78rem;color:var(--text-3);margin-top:.25rem"><?php echo date('F Y'); ?></div>
  </div>

</div>

<!-- Accesos rápidos -->
<div class="card">
  <div class="card-header"><h3>Accesos Rápidos</h3></div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.75rem">
      <?php
      $links = [
        ['📋','Órdenes Compra', '../orden_compra/ordencompra.php'],
        ['📥','Notas Entrada',  '../nota_entrada/notaentrada.php'],
        ['📤','PECOSA',         '../pecosa/pecosa.php'],
        ['🏢','Proveedores',    '../enlaces/proveedores.php'],
        ['📦','Productos',      '../enlaces/productos.php'],
        ['📊','Stock',          '../procesa_stock/stock.php'],
      ];
      foreach ($links as $l): ?>
      <a href="<?php echo $l[2]; ?>" class="btn btn-secondary"
         style="flex-direction:column;height:72px;gap:.35rem;font-size:.8rem;">
        <span style="font-size:1.3rem"><?php echo $l[0]; ?></span>
        <?php echo $l[1]; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>