/**
 * app.js — JavaScript principal del Sistema de Abastecimiento.
 *
 * Dependencias (cargadas antes en el layout):
 *   - jQuery 3.x
 *   - Bootstrap 5 bundle
 *
 * REGLA: Todas las llamadas al servidor deben ser Ajax (fetch o $.ajax).
 *        Nunca uses iframes ni formularios POST directos para actualizar
 *        parcialmente la página.
 */

'use strict';

$(function () {

    /* ════════════════════════════════════════════════════════════
       0. Barra de navegación: móvil y accesibilidad
       ════════════════════════════════════════════════════════════ */

    (function initMainNavbar() {
        const navbarCollapse = document.getElementById('navbarMain');
        if (!navbarCollapse || typeof bootstrap === 'undefined') {
            return;
        }

        const collapse = bootstrap.Collapse.getOrCreateInstance(navbarCollapse, { toggle: false });

        function hideNavbarMobile() {
            if (window.innerWidth < 992 && navbarCollapse.classList.contains('show')) {
                collapse.hide();
            }
        }

        navbarCollapse
            .querySelectorAll('.dropdown-item[href], a.nav-link[href]:not(.dropdown-toggle)')
            .forEach(function (anchor) {
            const href = anchor.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) {
                return;
            }
            anchor.addEventListener('click', function () {
                hideNavbarMobile();
            });
        });

        // Cerrar dropdown abierto con Escape (refuerzo accesible)
        document.addEventListener('keydown', function (ev) {
            if (ev.key !== 'Escape') {
                return;
            }
            const openToggle = document.querySelector('.navbar .dropdown-toggle.show');
            if (openToggle) {
                bootstrap.Dropdown.getOrCreateInstance(openToggle).hide();
            }
        });
    })();

    /* ════════════════════════════════════════════════════════════
       1. Helper global de Ajax (envuelve $.ajax con manejo de errores)
       ════════════════════════════════════════════════════════════ */

    /**
     * Realiza una petición Ajax y devuelve una Promise.
     *
     * @param {string} url
     * @param {Object} [options]
     * @param {string} [options.method='GET']
     * @param {Object} [options.data={}]
     * @returns {Promise<any>}
     */
    window.appAjax = function (url, options = {}) {
        const defaults = {
            method      : 'GET',
            data        : {},
            contentType : 'application/x-www-form-urlencoded',
            dataType    : 'json',
        };
        const cfg = Object.assign(defaults, options);

        return new Promise((resolve, reject) => {
            $.ajax({
                url        : url,
                type       : cfg.method,
                data       : cfg.data,
                contentType: cfg.contentType,
                dataType   : cfg.dataType,
                headers    : { 'X-Requested-With': 'XMLHttpRequest' },
                success    : resolve,
                error      : (jqXHR) => {
                    const msg = jqXHR.responseJSON?.error
                              ?? jqXHR.responseText
                              ?? 'Error de red';
                    reject(new Error(msg));
                },
            });
        });
    };

    /* ════════════════════════════════════════════════════════════
       2. Prueba Ajax: botón Ping en la página de inicio
       ════════════════════════════════════════════════════════════ */

    const $btnPing     = $('#btn-ping');
    const $pingResult  = $('#ping-result');

    if ($btnPing.length) {
        $btnPing.on('click', function () {
            $btnPing.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Enviando…'
            );
            $pingResult.html('');

            const pingUrl = (typeof window.APP_URL_BASE === 'string' && window.APP_URL_BASE.length)
                ? (window.APP_URL_BASE.replace(/\/$/, '') + '/api/ping')
                : '/api/ping';

            appAjax(pingUrl)
                .then(function (data) {
                    $pingResult.html(
                        '<div class="alert alert-success alert-sm py-2 mb-0">' +
                            '<i class="bi bi-check-circle me-2"></i>' +
                            '<strong>' + escHtml(data.message) + '</strong>' +
                            '<br><small class="text-muted">Timestamp: ' + escHtml(data.timestamp) + '</small>' +
                        '</div>'
                    );
                })
                .catch(function (err) {
                    $pingResult.html(
                        '<div class="alert alert-danger alert-sm py-2 mb-0">' +
                            '<i class="bi bi-x-circle me-2"></i>' + escHtml(err.message) +
                        '</div>'
                    );
                })
                .finally(function () {
                    $btnPing.prop('disabled', false).html(
                        '<i class="bi bi-send me-1"></i>Hacer Ping'
                    );
                });
        });
    }

    /* ════════════════════════════════════════════════════════════
       3. Utilidades globales
       ════════════════════════════════════════════════════════════ */

    /**
     * Escapa caracteres HTML para inserción segura en el DOM.
     * Úsala siempre que construyas HTML dinámico con datos del servidor.
     *
     * @param {any} str
     * @returns {string}
     */
    function escHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Exportar para uso desde otros módulos JS
    window.escHtml = escHtml;

    /**
     * Muestra un toast Bootstrap programáticamente.
     *
     * @param {string} message
     * @param {'success'|'danger'|'warning'|'info'} type
     */
    window.showToast = function (message, type = 'info') {
        const id = 'toast-' + Date.now();
        const html = `
            <div id="${id}" class="toast align-items-center text-bg-${type} border-0"
                 role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${escHtml(message)}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                </div>
            </div>`;

        let $container = $('#toast-container');
        if (!$container.length) {
            $container = $('<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
            $('body').append($container);
        }

        $container.append(html);
        const toastEl = document.getElementById(id);
        const toast   = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    };

    /* ════════════════════════════════════════════════════════════
       4. Confirmación antes de eliminar (delegación de eventos)
       ════════════════════════════════════════════════════════════ */
    $(document).on('click', '[data-confirm]', function (e) {
        const msg = $(this).data('confirm') || '¿Estás seguro?';
        if (!window.confirm(msg)) {
            e.preventDefault();
        }
    });

});
