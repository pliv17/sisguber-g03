/**
 * public/js/logistica/list.js
 * Listado genérico de Logística — Ajax JSON, paginación, modal anulación.
 *
 * Mismo patrón que maestros/list.js.
 * Requiere: jQuery, Bootstrap 5, window.APP_URL_BASE, window.showToast.
 */
(function ($) {
    'use strict';

    function apiUrl(path) {
        const base = (typeof window.APP_URL_BASE === 'string' && window.APP_URL_BASE.length)
            ? window.APP_URL_BASE.replace(/\/$/, '')
            : '';
        return base + path;
    }

    function csrf() {
        return $('meta[name="csrf-token"]').attr('content') || '';
    }

    function toast(msg, type) {
        if (typeof window.showToast === 'function') {
            window.showToast(msg, type || 'info');
        } else {
            window.alert(msg);
        }
    }

    function esc(s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function money(v) {
        return 'S/ ' + parseFloat(v || 0).toLocaleString('es-PE', { minimumFractionDigits: 2 });
    }

    function estadoBadge(v) {
        const map = {
            1: ['success', 'Normal'],
            2: ['danger',  'Anulada'],
        };
        const [cls, lbl] = map[parseInt(v)] ?? ['secondary', String(v)];
        return `<span class="badge bg-${cls}">${esc(lbl)}</span>`;
    }

    $(function () {
        const $root = $('#logistica-app');
        if (!$root.length) return;

        let cfg;
        try {
            cfg = JSON.parse($root.attr('data-config') || '{}');
        } catch (e) {
            console.error('logistica/list.js: config inválida', e);
            return;
        }

        // ── Estado ────────────────────────────────────────────
        const state = {
            page: 1, perPage: 15,
            filters: {},
            cancelNorden: null, cancelYear: null,
        };

        const $tbody   = $('#logistica-tbody');
        const $thead   = $('#logistica-thead');
        const $pag     = $('#logistica-pagination');
        const $loading = $('#logistica-loading');
        const $count   = $('#logistica-count');
        const modalCancel = document.getElementById('logisticaCancelModal')
            ? new bootstrap.Modal(document.getElementById('logisticaCancelModal'))
            : null;

        // ── Cabecera de la tabla ──────────────────────────────
        function buildHeaders() {
            let html = '';
            (cfg.columns || []).forEach(function (c) {
                const align = c.right ? 'text-end' : (c.center ? 'text-center' : '');
                html += `<th scope="col" class="${align}">${esc(c.label)}</th>`;
            });
            // Columna acciones si hay row_actions
            if ((cfg.row_actions || []).length) {
                html += '<th scope="col" class="text-end" style="width:7rem">Acciones</th>';
            }
            $thead.html(html);
        }

        // ── Construir query string ────────────────────────────
        function buildQuery() {
            const params = new URLSearchParams();
            params.set('page',     String(state.page));
            params.set('per_page', String(state.perPage));
            Object.entries(state.filters).forEach(([k, v]) => {
                if (v !== '' && v !== null && v !== undefined) params.set(k, String(v));
            });
            return params.toString();
        }

        // ── Leer filtros del DOM ──────────────────────────────
        function readFilters() {
            const f = {};
            $('.logistica-filter').each(function () {
                const key = $(this).data('filter');
                const val = $(this).val();
                if (key) f[key] = val;
            });
            return f;
        }

        // ── Cargar datos ──────────────────────────────────────
        function load() {
            $loading.show();
            $tbody.html('<tr><td colspan="99" class="text-muted p-3">Cargando…</td></tr>');

            $.ajax({
                url:      apiUrl(cfg.api || '') + '?' + buildQuery(),
                method:   'GET',
                dataType: 'json',
            })
            .done(function (res) {
                renderRows(res.data || []);
                renderPagination(res.meta || {});
                $count.text((res.meta && res.meta.total) ? res.meta.total : (res.data || []).length);
            })
            .fail(function (xhr) {
                const msg = xhr.responseJSON?.message || 'Error al cargar datos.';
                $tbody.html(`<tr><td colspan="99" class="text-danger p-3">${esc(msg)}</td></tr>`);
                toast(msg, 'danger');
            })
            .always(function () {
                $loading.hide();
            });
        }

        // ── Renderizar filas ──────────────────────────────────
        function renderRows(rows) {
            if (!rows.length) {
                $tbody.html('<tr><td colspan="99" class="text-muted p-3">Sin resultados para los filtros aplicados.</td></tr>');
                return;
            }

            let html = '';
            rows.forEach(function (row, idx) {
                html += '<tr>';
                (cfg.columns || []).forEach(function (c) {
                    const val = row[c.key] ?? '';
                    const align = c.right ? 'text-end' : (c.center ? 'text-center' : '');
                    let cell;
                    if (c.badge)  cell = estadoBadge(val);
                    else if (c.money) cell = esc(money(val));
                    else if (c.mono)  cell = `<code>${esc(val)}</code>`;
                    else              cell = esc(String(val));
                    html += `<td class="${align}">${cell}</td>`;
                });

                // Acciones
                if ((cfg.row_actions || []).includes('cancel')) {
                    const canCancel = parseInt(row.estado) === 1;
                    html += `<td class="text-end">
                        <button class="btn btn-outline-danger btn-sm py-0 px-2 btn-cancel-row"
                            data-norden="${esc(row.norden)}"
                            data-year="${esc(row.year)}"
                            ${canCancel ? '' : 'disabled title="Ya anulada"'}>
                            <i class="bi bi-slash-circle"></i>
                        </button>
                    </td>`;
                }

                html += '</tr>';
            });
            $tbody.html(html);
        }

        // ── Paginación ────────────────────────────────────────
        function renderPagination(meta) {
            if (!meta.total_pages || meta.total_pages <= 1) { $pag.html(''); return; }
            let html = '';
            const cur = meta.current_page || 1;
            const tot = meta.total_pages;

            html += `<li class="page-item ${cur <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${cur - 1}">‹</a></li>`;

            for (let p = Math.max(1, cur - 2); p <= Math.min(tot, cur + 2); p++) {
                html += `<li class="page-item ${p === cur ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
            }

            html += `<li class="page-item ${cur >= tot ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${cur + 1}">›</a></li>`;

            $pag.html(html);
        }

        // ── Eventos ───────────────────────────────────────────
        buildHeaders();

        // Buscar
        $('#logistica-btn-search').on('click', function () {
            state.page    = 1;
            state.filters = readFilters();
            load();
        });

        // Limpiar
        $('#logistica-btn-reset').on('click', function () {
            $('.logistica-filter').each(function () {
                const tag = this.tagName.toLowerCase();
                if (tag === 'select') $(this).prop('selectedIndex', 0);
                else $(this).val('');
            });
            state.page    = 1;
            state.filters = readFilters();
            load();
        });

        // Paginación
        $pag.on('click', 'a.page-link', function (e) {
            e.preventDefault();
            const p = parseInt($(this).data('page'));
            if (!isNaN(p) && p !== state.page) {
                state.page = p;
                load();
            }
        });

        // Anular — abrir modal
        $tbody.on('click', '.btn-cancel-row', function () {
            state.cancelNorden = $(this).data('norden');
            state.cancelYear   = parseInt($(this).data('year'));
            $('#cancel-norden-label').text(state.cancelNorden);
            if (modalCancel) modalCancel.show();
        });

        // Anular — confirmar
        $('#logistica-btn-confirm-cancel').on('click', function () {
            if (!state.cancelNorden) return;

            const url = apiUrl(cfg.api + '/' + encodeURIComponent(state.cancelNorden)
                             + '/' + state.cancelYear + '/cancelar');

            $.ajax({
                url,
                method:  'POST',
                headers: { 'X-CSRF-Token': csrf(), 'Content-Type': 'application/json' },
                data:    JSON.stringify({}),
            })
            .done(function () {
                if (modalCancel) modalCancel.hide();
                toast('Orden anulada correctamente.', 'success');
                load();
            })
            .fail(function (xhr) {
                const msg = xhr.responseJSON?.message || 'Error al anular.';
                toast(msg, 'danger');
            });
        });

        // Carga inicial
        state.filters = readFilters();
        load();
    });

}(jQuery));
