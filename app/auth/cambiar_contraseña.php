<?php
/**
 * cambiar_contrasena.php - SisGuber G03
 * Vista HTML. Solo presentación.
 * Lógica en: cambiar_contrasena_datos.php
 * Estilos en: cambiar_contrasena.css + ../assets/css/global.css
 * Scripts en: cambiar_contrasena.js
 */
define('APP_ACCESS', true);
require_once __DIR__ . '/cambiar_contrasena_datos.php';
$forzado = isset($_GET['forzado']) || isset($_POST['forzado']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SisGuber — Cambiar Contraseña</title>
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="cambiar_contrasena.css">
</head>
<body>

<div class="password-page">
  <div class="password-card">

    <!-- Encabezado -->
    <div class="card-top">
      <div class="icon">🔑</div>
      <div>
        <h1>Cambiar Contraseña</h1>
        <p>SisGuber — <?php echo htmlspecialchars($_SESSION['nombres'] . ' ' . $_SESSION['apellidos']); ?></p>
      </div>
    </div>

    <div class="card-inner">

      <!-- Aviso si es cambio forzado -->
      <?php if ($forzado): ?>
      <div class="forced-notice">
        <span>⚠️</span>
        <span>Por seguridad, debe cambiar su contraseña antes de continuar. Esta es la primera vez que ingresa al sistema.</span>
      </div>
      <?php endif; ?>

      <!-- Alertas -->
      <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <!-- Formulario -->
      <form method="POST" action="cambiar_contrasena.php" id="pwForm" novalidate>
        <?php if ($forzado): ?>
        <input type="hidden" name="forzado" value="1">
        <?php endif; ?>

        <div class="form-group">
          <label class="form-label" for="actual">Contraseña Actual</label>
          <div class="password-wrapper">
            <input class="form-control" type="password" id="actual" name="actual"
                   placeholder="Ingrese su contraseña actual" required>
            <button type="button" class="password-toggle" data-target="actual">👁️</button>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="nueva">Nueva Contraseña</label>
          <div class="password-wrapper">
            <input class="form-control" type="password" id="nueva" name="nueva"
                   placeholder="Mínimo 6 caracteres" required
                   oninput="evaluarFuerza(this.value)">
            <button type="button" class="password-toggle" data-target="nueva">👁️</button>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
          <span class="strength-label" id="strengthLabel"></span>
        </div>

        <!-- Requisitos -->
        <div class="requirements">
          <p>La contraseña debe tener:</p>
          <div class="req-item" id="req-len"><span class="req-icon">○</span> Al menos 6 caracteres</div>
          <div class="req-item" id="req-upper"><span class="req-icon">○</span> Una letra mayúscula</div>
          <div class="req-item" id="req-num"><span class="req-icon">○</span> Un número</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="confirma">Confirmar Nueva Contraseña</label>
          <div class="password-wrapper">
            <input class="form-control" type="password" id="confirma" name="confirma"
                   placeholder="Repita la nueva contraseña" required>
            <button type="button" class="password-toggle" data-target="confirma">👁️</button>
          </div>
        </div>

        <div style="display:flex; gap:.75rem; margin-top:.5rem">
          <button type="submit" class="btn btn-primary" style="flex:1">
            💾 Guardar Contraseña
          </button>
          <?php if (!$forzado): ?>
          <a href="../layout/index2.php" class="btn btn-secondary">Cancelar</a>
          <?php endif; ?>
        </div>

      </form>
    </div>
  </div>
</div>

<script src="cambiar_contrasena.js"></script>
</body>
</html>