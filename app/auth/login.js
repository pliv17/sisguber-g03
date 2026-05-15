/**
 * login.js - SisGuber G03
 * Comportamiento JavaScript de la página de login.
 * Separado del HTML y del CSS.
 */

document.addEventListener('DOMContentLoaded', function () {

  // ---- MOSTRAR / OCULTAR CONTRASEÑA ----
  var toggleBtn = document.getElementById('togglePassword');
  var passwordInput = document.getElementById('password');

  if (toggleBtn && passwordInput) {
    toggleBtn.addEventListener('click', function () {
      var isPassword = passwordInput.type === 'password';
      passwordInput.type = isPassword ? 'text' : 'password';
      toggleBtn.textContent = isPassword ? '🙈' : '👁️';
    });
  }

  // ---- VALIDACIÓN DEL FORMULARIO ----
  var form = document.getElementById('loginForm');
  var loginBtn = document.getElementById('loginBtn');
  var usernameInput = document.getElementById('username');

  if (form) {
    form.addEventListener('submit', function (e) {
      var valid = true;

      // Limpiar errores previos
      clearErrors();

      // Validar usuario
      if (!usernameInput.value.trim()) {
        showFieldError(usernameInput, 'El usuario es requerido.');
        valid = false;
      }

      // Validar contraseña
      if (!passwordInput.value.trim()) {
        showFieldError(passwordInput, 'La contraseña es requerida.');
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
        return;
      }

      // Estado de carga
      if (loginBtn) {
        loginBtn.disabled = true;
        loginBtn.textContent = 'Verificando...';
      }
    });
  }

  // ---- OCULTAR ALERTA AUTOMÁTICAMENTE ----
  var errorAlert = document.getElementById('loginError');
  if (errorAlert) {
    setTimeout(function () {
      errorAlert.style.transition = 'opacity .4s';
      errorAlert.style.opacity = '0';
      setTimeout(function () { errorAlert.style.display = 'none'; }, 400);
    }, 5000);
  }

  // ---- FOCUS AUTOMÁTICO ----
  if (usernameInput && !usernameInput.value) {
    usernameInput.focus();
  } else if (passwordInput) {
    passwordInput.focus();
  }

  // ---- FUNCIONES AUXILIARES ----
  function showFieldError(input, msg) {
    input.classList.add('is-error');
    var hint = document.createElement('span');
    hint.className = 'form-error field-error-msg';
    hint.textContent = msg;
    input.parentNode.appendChild(hint);
  }

  function clearErrors() {
    document.querySelectorAll('.is-error').forEach(function (el) {
      el.classList.remove('is-error');
    });
    document.querySelectorAll('.field-error-msg').forEach(function (el) {
      el.remove();
    });
  }

});