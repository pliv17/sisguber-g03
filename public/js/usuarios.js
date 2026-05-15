/**
 * usuarios.js - SisGuber G03
 * Comportamiento JS de la gestión de usuarios.
 */

document.addEventListener('DOMContentLoaded', function () {

  // ---- CONFIRMAR ELIMINACIÓN ----
  document.querySelectorAll('.btn-eliminar').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      var nombre = btn.getAttribute('data-nombre');
      if (!confirm('¿Eliminar al usuario "' + nombre + '"?\nEsta acción no se puede deshacer.')) {
        e.preventDefault();
      }
    });
  });

  // ---- TOGGLE FORMULARIO ----
  var toggleBtn  = document.getElementById('toggleForm');
  var formPanel  = document.getElementById('userFormPanel');

  if (toggleBtn && formPanel) {
    toggleBtn.addEventListener('click', function () {
      var visible = formPanel.style.display !== 'none';
      formPanel.style.display = visible ? 'none' : 'block';
      toggleBtn.textContent   = visible ? '+ Nuevo Usuario' : '✕ Cancelar';
    });
  }

  // Si hay errores o se está editando, mostrar el formulario
  var hayError  = document.getElementById('hayError');
  var hayEditar = document.getElementById('hayEditar');
  if ((hayError || hayEditar) && formPanel) {
    formPanel.style.display = 'block';
    if (toggleBtn) toggleBtn.textContent = '✕ Cancelar';
  }

  // ---- BÚSQUEDA EN TIEMPO REAL ----
  var searchInput = document.getElementById('searchUsuario');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      var term = this.value.toLowerCase();
      document.querySelectorAll('tbody tr').forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
      });
    });
  }

});