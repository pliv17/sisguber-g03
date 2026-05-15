/**
 * cambiar_contrasena.js - SisGuber G03
 * Comportamiento JS de la página de cambio de contraseña.
 */

document.addEventListener('DOMContentLoaded', function () {

  // ---- MOSTRAR/OCULTAR CONTRASEÑAS ----
  document.querySelectorAll('.password-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var targetId = btn.getAttribute('data-target');
      var input = document.getElementById(targetId);
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.textContent = input.type === 'password' ? '👁️' : '🙈';
    });
  });

  // ---- MATCH DE CONFIRMACIÓN ----
  var nuevaInput    = document.getElementById('nueva');
  var confirmaInput = document.getElementById('confirma');

  if (confirmaInput) {
    confirmaInput.addEventListener('input', function () {
      if (nuevaInput.value && confirmaInput.value) {
        if (nuevaInput.value !== confirmaInput.value) {
          confirmaInput.classList.add('is-error');
        } else {
          confirmaInput.classList.remove('is-error');
        }
      }
    });
  }

  // ---- VALIDACIÓN ANTES DE ENVIAR ----
  var form = document.getElementById('pwForm');
  if (form) {
    form.addEventListener('submit', function (e) {
      if (nuevaInput.value !== confirmaInput.value) {
        e.preventDefault();
        confirmaInput.classList.add('is-error');
        confirmaInput.focus();
      }
    });
  }
});

/**
 * Evalúa la fortaleza de la contraseña nueva y actualiza indicadores visuales.
 * @param {string} val - Valor del campo de nueva contraseña
 */
function evaluarFuerza(val) {
  var fill  = document.getElementById('strengthFill');
  var label = document.getElementById('strengthLabel');
  var reqLen   = document.getElementById('req-len');
  var reqUpper = document.getElementById('req-upper');
  var reqNum   = document.getElementById('req-num');

  if (!fill) return;

  var score = 0;
  var checks = {
    len:   val.length >= 6,
    upper: /[A-Z]/.test(val),
    num:   /[0-9]/.test(val),
    special: /[^A-Za-z0-9]/.test(val),
    long: val.length >= 10
  };

  // Actualizar requisitos visuales
  setReq(reqLen,   checks.len);
  setReq(reqUpper, checks.upper);
  setReq(reqNum,   checks.num);

  // Calcular puntaje
  if (checks.len)     score += 25;
  if (checks.upper)   score += 25;
  if (checks.num)     score += 25;
  if (checks.special) score += 15;
  if (checks.long)    score += 10;

  // Aplicar estilos
  fill.style.width = Math.min(score, 100) + '%';

  if (score < 30) {
    fill.style.background  = '#ef4444';
    label.textContent      = 'Débil';
    label.style.color      = '#ef4444';
  } else if (score < 60) {
    fill.style.background  = '#f59e0b';
    label.textContent      = 'Regular';
    label.style.color      = '#d97706';
  } else if (score < 80) {
    fill.style.background  = '#3b82f6';
    label.textContent      = 'Buena';
    label.style.color      = '#1d4ed8';
  } else {
    fill.style.background  = '#10b981';
    label.textContent      = 'Fuerte ✓';
    label.style.color      = '#065f46';
  }

  if (!val) { fill.style.width = '0'; label.textContent = ''; }
}

function setReq(el, ok) {
  if (!el) return;
  el.classList.toggle('ok', ok);
  el.querySelector('.req-icon').textContent = ok ? '✓' : '○';
}