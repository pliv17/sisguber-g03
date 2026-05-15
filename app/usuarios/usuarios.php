<?php
/**
 * usuarios.php - SisGuber G03
 * Vista HTML del módulo de gestión de usuarios.
 * Lógica en: usuarios_datos.php
 * Estilos en: usuarios.css
 * Scripts en: usuarios.js
 */
define('APP_ACCESS', true);
require_once __DIR__ . '/usuarios_datos.php';

$pageTitle  = 'Gestión de Usuarios';
$activeMenu = 'usuarios';
$extraCSS   = ['usuarios.css'];
$extraJS    = ['usuarios.js'];

require_once __DIR__ . '/../layout/_layout.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
  <a href="../layout/index2.php">🏠 Inicio</a>
  <span>›</span> <span>Administración</span>
  <span>›</span> <span>Usuarios</span>
</div>

<!-- Page header -->
<div class="page-header">
  <div class="page-title">
    <h1>👥 Gestión de Usuarios</h1>
    <p>Administración de cuentas y niveles de acceso</p>
  </div>
  <button class="btn btn-primary" id="toggleForm">+ Nuevo Usuario</button>
</div>

<!-- Alertas -->
<?php if (!empty($success)): ?>
<div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="alert alert-danger" id="hayError">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($usuarioEditar): ?><span id="hayEditar" style="display:none"></span><?php endif; ?>

<!-- =================== FORMULARIO =================== -->
<div class="user-form-panel" id="userFormPanel"
     style="display:<?php echo ($error || $usuarioEditar) ? 'block' : 'none' ?>">

  <div class="panel-header">
    <span style="font-size:1.1rem"><?php echo $usuarioEditar ? '✏️' : '➕'; ?></span>
    <h3><?php echo $usuarioEditar ? 'Editar Usuario: ' . htmlspecialchars($usuarioEditar['usuario']) : 'Nuevo Usuario'; ?></h3>
  </div>

  <div class="panel-body">
    <form method="POST" action="usuarios.php" id="userForm" novalidate>
      <?php if ($usuarioEditar): ?>
      <input type="hidden" name="editando" value="<?php echo htmlspecialchars($usuarioEditar['usuario']); ?>">
      <?php endif; ?>

      <div class="form-grid-2">

        <div class="form-group">
          <label class="form-label">Usuario *</label>
          <input class="form-control" type="text" name="usuario" maxlength="20"
                 placeholder="Ej: jperez"
                 value="<?php echo htmlspecialchars($usuarioEditar['usuario'] ?? $_POST['usuario'] ?? ''); ?>"
                 <?php echo $usuarioEditar ? 'readonly style="background:#f1f5f9"' : ''; ?>
                 required>
          <span class="form-hint">Sin espacios, máx. 20 caracteres</span>
        </div>

        <div class="form-group">
          <label class="form-label">Nivel de Acceso</label>
          <select class="form-control" name="nivel">
            <option value="us" <?php echo ($usuarioEditar['nivel'] ?? '') === 'us' ? 'selected' : ''; ?>>Usuario</option>
            <option value="ad" <?php echo ($usuarioEditar['nivel'] ?? '') === 'ad' ? 'selected' : ''; ?>>Administrador</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Nombres *</label>
          <input class="form-control" type="text" name="nombres" maxlength="60"
                 placeholder="Nombres del usuario"
                 value="<?php echo htmlspecialchars($usuarioEditar['nombres'] ?? $_POST['nombres'] ?? ''); ?>"
                 required>
        </div>

        <div class="form-group">
          <label class="form-label">Apellidos</label>
          <input class="form-control" type="text" name="apellidos" maxlength="60"
                 placeholder="Apellidos del usuario"
                 value="<?php echo htmlspecialchars($usuarioEditar['apellidos'] ?? $_POST['apellidos'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Correo Electrónico</label>
          <input class="form-control" type="email" name="correo" maxlength="80"
                 placeholder="correo@ejemplo.com"
                 value="<?php echo htmlspecialchars($usuarioEditar['correo'] ?? $_POST['correo'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Oficina</label>
          <input class="form-control" type="text" name="oficina" maxlength="60"
                 placeholder="Área u oficina"
                 value="<?php echo htmlspecialchars($usuarioEditar['oficina'] ?? $_POST['oficina'] ?? ''); ?>">
        </div>

        <?php if ($usuarioEditar): ?>
        <div class="form-group">
          <label class="form-label">Nueva Contraseña (opcional)</label>
          <input class="form-control" type="password" name="nueva_clave"
                 placeholder="Dejar vacío para no cambiar">
          <span class="form-hint">Si se ingresa, el usuario deberá cambiarla al siguiente acceso.</span>
        </div>
        <?php endif; ?>

      </div>

      <div style="display:flex;gap:.75rem;margin-top:.5rem">
        <button type="submit" class="btn btn-primary">
          <?php echo $usuarioEditar ? '💾 Guardar Cambios' : '➕ Crear Usuario'; ?>
        </button>
        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
      </div>

      <?php if (!$usuarioEditar): ?>
      <p class="form-hint" style="margin-top:.5rem">
        La contraseña por defecto para nuevos usuarios es <strong>123456</strong>.
        El usuario deberá cambiarla al primer acceso.
      </p>
      <?php endif; ?>

    </form>
  </div>
</div>

<!-- =================== TABLA =================== -->
<div class="card">
  <div class="card-header">
    <h3>Usuarios del Sistema <span class="badge badge-gray"><?php echo $totalUsuarios; ?></span></h3>
    <input type="text" id="searchUsuario" class="form-control"
           placeholder="Filtrar usuarios..."
           style="width:220px;font-size:.82rem;padding:.4rem .75rem">
  </div>
  <div style="overflow-x:auto">
    <table class="table-std">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Nombre Completo</th>
          <th>Oficina</th>
          <th>Correo</th>
          <th>Nivel</th>
          <th>Verificado</th>
          <th style="text-align:center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($totalUsuarios === 0): ?>
        <tr>
          <td colspan="7" style="text-align:center;padding:2rem;color:var(--text-3)">
            No hay usuarios registrados.
          </td>
        </tr>
        <?php else: while ($row = mysqli_fetch_assoc($resUsuarios)): ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:.6rem">
              <div class="user-avatar-lg"><?php echo strtoupper(substr($row['nombres'],0,1).substr($row['apellidos'],0,1)); ?></div>
              <strong style="font-size:.85rem"><?php echo htmlspecialchars($row['usuario']); ?></strong>
            </div>
          </td>
          <td><?php echo htmlspecialchars($row['nombres'] . ' ' . $row['apellidos']); ?></td>
          <td style="font-size:.82rem;color:var(--text-2)"><?php echo htmlspecialchars($row['oficina']); ?></td>
          <td style="font-size:.82rem;color:var(--text-2)"><?php echo htmlspecialchars($row['correo']); ?></td>
          <td>
            <span class="badge <?php echo $row['nivel']==='ad' ? 'level-badge-ad' : 'level-badge-us'; ?>">
              <?php echo $row['nivel'] === 'ad' ? 'Admin' : 'Usuario'; ?>
            </span>
          </td>
          <td>
            <span class="verified-dot <?php echo $row['verificado'] ? 'yes' : 'no'; ?>"></span>
            <?php echo $row['verificado'] ? 'Sí' : 'No'; ?>
          </td>
          <td>
            <div style="display:flex;gap:.4rem;justify-content:center">
              <a href="usuarios.php?accion=editar&usuario=<?php echo urlencode($row['usuario']); ?>"
                 class="btn btn-secondary btn-sm" title="Editar">✏️</a>
              <?php if ($row['usuario'] !== $_SESSION['usuario']): ?>
              <a href="usuarios.php?accion=eliminar&usuario=<?php echo urlencode($row['usuario']); ?>"
                 class="btn btn-danger btn-sm btn-eliminar"
                 data-nombre="<?php echo htmlspecialchars($row['usuario']); ?>"
                 title="Eliminar">🗑️</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../layout/_layout_end.php'; ?>