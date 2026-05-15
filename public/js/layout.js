/**
 * layout.js - SisGuber G03
 * Comportamiento del layout: sidebar, navbar dropdown, submenú.
 * Incluir en todas las páginas del sistema (lo hace _layout.php).
 */

document.addEventListener('DOMContentLoaded', function () {

  var sidebar     = document.getElementById('sidebar');
  var mainContent = document.getElementById('mainContent');
  var toggleBtn   = document.getElementById('sidebarToggle');
  var overlay     = document.getElementById('sidebarOverlay');
  var isMobile    = window.innerWidth <= 768;

  // ---- TOGGLE SIDEBAR ----
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      if (isMobile) {
        sidebar.classList.toggle('mobile-open');
        overlay && overlay.classList.toggle('active');
      } else {
        sidebar.classList.toggle('collapsed');
        mainContent && mainContent.classList.toggle('expanded');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
      }
    });
  }

  // Restaurar estado del sidebar
  if (!isMobile && localStorage.getItem('sidebarCollapsed') === 'true') {
    sidebar && sidebar.classList.add('collapsed');
    mainContent && mainContent.classList.add('expanded');
  }

  // Cerrar sidebar en mobile al hacer clic en overlay
  if (overlay) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('mobile-open');
      overlay.classList.remove('active');
    });
  }

  // ---- SUBMENÚS ----
  document.querySelectorAll('.nav-item[data-submenu]').forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var targetId  = item.getAttribute('data-submenu');
      var submenu   = document.getElementById(targetId);
      if (!submenu) return;

      var isOpen = submenu.classList.contains('open');

      // Cerrar todos
      document.querySelectorAll('.nav-submenu.open').forEach(function (sm) {
        sm.classList.remove('open');
      });
      document.querySelectorAll('.nav-item.open').forEach(function (it) {
        it.classList.remove('open');
      });

      // Abrir el seleccionado
      if (!isOpen) {
        submenu.classList.add('open');
        item.classList.add('open');
      }
    });
  });

  // Marcar ítem activo según URL actual
  var currentPath = window.location.pathname;
  document.querySelectorAll('.nav-item[href]').forEach(function (link) {
    if (currentPath.endsWith(link.getAttribute('href'))) {
      link.classList.add('active');
      // Abrir submenú padre si existe
      var parent = link.closest('.nav-submenu');
      if (parent) {
        parent.classList.add('open');
        var parentItem = document.querySelector('[data-submenu="' + parent.id + '"]');
        if (parentItem) parentItem.classList.add('open');
      }
    }
  });

  // ---- DROPDOWN DE USUARIO ----
  var userBtn      = document.getElementById('userMenuBtn');
  var userDropdown = document.getElementById('userDropdown');

  if (userBtn && userDropdown) {
    userBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      userDropdown.classList.toggle('open');
    });

    document.addEventListener('click', function (e) {
      if (!userBtn.contains(e.target)) {
        userDropdown.classList.remove('open');
      }
    });
  }

  // ---- RESIZE ----
  window.addEventListener('resize', function () {
    isMobile = window.innerWidth <= 768;
    if (!isMobile) {
      sidebar && sidebar.classList.remove('mobile-open');
      overlay && overlay.classList.remove('active');
    }
  });

});