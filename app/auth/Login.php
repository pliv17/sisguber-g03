<?php
/**
 * login.php - SisGuber G03
 * Vista HTML del login. Solo presentación.
 * Lógica en: login_datos.php
 * Estilos en: login.css + ../assets/css/global.css
 * Scripts en: login.js
 */
define('APP_ACCESS', true);
require_once __DIR__ . '/login_datos.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SisGuber — Inicio de Sesión</title>
  <!-- CSS SEPARADO -->
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-page">

  <!-- LADO IZQUIERDO — Branding -->
  <div class="login-left">
    <div class="login-brand">

      <div class="logo">
        <div class="logo-icon">🏛️</div>
        <div class="logo-text">
          <h1>SisGuber</h1>
          <p>Sistema de Abastecimiento y Almacén</p>
        </div>
      </div>

      <p class="description">
        Sistema Integrado al Servicio Gubernamental. Gestión eficiente
        de órdenes de compra, notas de entrada, PECOSAS, stock e inventario
        para la Municipalidad Distrital de La Unión.
      </p>

      <div class="features">
        <div class="feature-item">
          <div class="feature-icon">📦</div>
          <span>Control de stock e inventario en tiempo real</span>
        </div>
        <div class="feature-item">
          <div class="feature-icon">📋</div>
          <span>Órdenes de Compra y Servicio digitales</span>
        </div>
        <div class="feature-item">
          <div class="feature-icon">🏢</div>
          <span>Gestión de proveedores y catálogo de bienes</span>
        </div>
        <div class="feature-item">
          <div class="feature-icon">📊</div>
          <span>Reportes en PDF y kardex automático</span>
        </div>
      </div>

    </div>
  </div>

  <!-- LADO DERECHO — Formulario -->
  <div class="login-right">
    <div class="login-card">

      <h2 class="card-title">Bienvenido</h2>
      <p class="card-subtitle">Ingrese sus credenciales para continuar</p>

      <div class="municipalidad">
        <img src="../img/logo.jpg" alt="Logo" onerror="this.style.display='none'">
        <span>Municipalidad Distrital de La Unión</span>
      </div>

      <!-- Alerta de error -->
      <?php if (!empty($error)): ?>
      <div class="alert alert-danger" id="loginError">
        <span>⚠️</span>
        <span><?php echo htmlspecialchars($error); ?></span>
      </div>
      <?php endif; ?>

      <!-- Alerta si viene de logout -->
      <?php if (isset($_GET['logout'])): ?>
      <div class="alert alert-success">
        <span>✅</span>
        <span>Sesión cerrada correctamente.</span>
      </div>
      <?php endif; ?>

      <!-- Formulario de login -->
      <form method="POST" action="login.php" id="loginForm" novalidate>

        <div class="form-group">
          <label class="form-label" for="username">Usuario</label>
          <input
            class="form-control"
            type="text"
            id="username"
            name="username"
            placeholder="Ingrese su usuario"
            maxlength="30"
            autocomplete="username"
            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
            required>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Contraseña</label>
          <div class="password-wrapper">
            <input
              class="form-control"
              type="password"
              id="password"
              name="password"
              placeholder="Ingrese su contraseña"
              maxlength="30"
              autocomplete="current-password"
              required>
            <button type="button" class="password-toggle" id="togglePassword"
                    aria-label="Mostrar/ocultar contraseña">👁️</button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
          Ingresar al Sistema
        </button>

      </form>

      <div class="divider"></div>

      <p class="footer-text">
        SisGuber v2.0 — PHP <?php echo PHP_VERSION; ?><br>
        © <?php echo date('Y'); ?> Municipalidad Distrital de La Unión
      </p>

    </div>
  </div>
</div>

<!-- JAVASCRIPT SEPARADO -->
<script src="login.js"></script>

</body>
</html>