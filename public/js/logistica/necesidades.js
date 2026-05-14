/**
 * public/js/logistica/necesidades.js
 * Cuadro de Necesidades — lógica de cliente con tabs.
 * Requiere: jQuery, Bootstrap 5, window.showToast.
 *
 * TODO: conectar a API real en /api/logistica/necesidades cuando esté lista.
 */
(function ($) {
    'use strict';

    function esc(s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function money(v) {
        return 'S/ ' + parseFloat(v || 0).toLocaleString('es-PE', { minimumFractionDigits: 2 });
    }

    // ── Demo data ──────────────────────────────────────────────
    const DEMO_LISTA = [
        { id: 'REQ-2026-00043', area: 'Gerencia de Administración', resp: 'Juan Torres',   fecha: '12/05/2026', items: 8,  valor: 14200, estado: 'pendiente', prog: 0   },
        { id: 'REQ-2026-00042', area: 'Recursos Humanos',           resp: 'María López',   fecha: '11/05/2026', items: 3,  valor: 2800,  estado: 'proceso',   prog: 60  },
        { id: 'REQ-2026-00041', area: 'Tesorería',                  resp: 'Carlos Quispe', fecha: '10/05/2026', items: 12, valor: 31500, estado: 'atendido',  prog: 100 },
        { id: 'REQ-2026-00040', area: 'Obras Públicas',             resp: 'Ana Mamani',    fecha: '09/05/2026', items: 5,  valor: 8400,  estado: 'pendiente', prog: 0   },
        { id: 'REQ-2026-00039', area: 'Gerencia Municipal',         resp: 'Luis Flores',   fecha: '08/05/2026', items: 6,  valor: 11200, estado: 'atendido',  prog: 100 },
    ];

    const DEMO_DETALLE = [
        { cod: '00000015', desc: 'PAPEL BOND A4 75gr x 500 Hjs', um: 'MILLAR', csol: 10, cat: 10, pu: 45.50, estado: 'atendido'  },
        { cod: '00000082', desc: 'TONER HP LASERJET CF217A',      um: 'UND',   csol: 6,  cat: 6,  pu: 180.0, estado: 'atendido'  },
        { cod: '00000103', desc: 'ARCHIVADOR DE PALANCA A4',      um: 'UND',   csol: 50, cat: 35, pu: 8.50,  estado: 'proceso'   },
        { cod: '00000211', desc: 'FOLDER MANILA A4',              um: 'CIENTO',csol: 5,  cat: 0,  pu: 12.00, estado: 'pendiente' },
    ];

    const ESTADO_CFG = {
        pendiente: { cls: 'warning', txt: 'Pendiente' },
        proceso:   { cls: 'info',    txt: 'En Proceso' },
        atendido:  { cls: 'success', txt: 'Atendido'   },
    };

    function badgeEstado(v) {
        const c = ESTADO_CFG[v] || { cls: 'secondary', txt: String(v) };
        return `<span class="badge bg-${c.cls} text-dark">${esc(c.txt)}</span>`;
    }

    function progBar(pct) {
        const cls = pct >= 100 ? 'bg-success' : (pct >= 50 ? 'bg-warning' : 'bg-danger');
        return `<div class="progress" style="height:6px;min-width:60px">
                  <div class="progress-bar ${cls}" style="width:${pct}%"></div>
                </div>
                <div class="text-center small text-muted">${pct}%</div>`;
    }

    // ── KPIs ───────────────────────────────────────────────────
    function updateKpis(data) {
        $('#cn-kpi-total').text(data.length);
        $('#cn-kpi-pend').text(data.filter(function (r) { return r.estado === 'pendiente'; }).length);
        $('#cn-kpi-proc').text(data.filter(function (r) { return r.estado === 'proceso'; }).length);
        $('#cn-kpi-aten').text(data.filter(function (r) { return r.estado === 'atendido'; }).length);
    }

    // ── Render lista ───────────────────────────────────────────
    function renderLista(data) {
        updateKpis(data);
        const $tbody = $('#cn-tbody-lista');
        if (!data.length) {
            $tbody.html('<tr><td colspan="10" class="text-muted p-3">Sin resultados.</td></tr>');
            return;
        }
        let html = '';
        data.forEach(function (r, i) {
            html += `<tr>
                <td class="text-muted">${i + 1}</td>
                <td><code>${esc(r.id)}</code></td>
                <td>${esc(r.area)}</td>
                <td class="small">${esc(r.resp)}</td>
                <td class="text-center font-monospace small">${esc(r.fecha)}</td>
                <td class="text-center">${r.items}</td>
                <td class="text-end font-monospace small">${money(r.valor)}</td>
                <td class="text-center">${badgeEstado(r.estado)}</td>
                <td>${progBar(r.prog)}</td>
                <td class="text-center">
                    <button class="btn btn-outline-primary btn-sm py-0 px-2 btn-ver-detalle"
                            data-id="${esc(r.id)}" data-area="${esc(r.area)}"
                            title="Ver detalle">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>`;
        });
        $tbody.html(html);
    }

    // ── Render detalle ─────────────────────────────────────────
    function renderDetalle(id, area) {
        // TODO: llamar a /api/logistica/necesidades/{id}/items
        const data = DEMO_DETALLE;
        const $tbody = $('#cn-tbody-detalle');
        $('#cn-detalle-info').html(
            `<i class="bi bi-info-circle me-1"></i>Requerimiento: <strong>${esc(id)}</strong> &nbsp;·&nbsp; ${esc(area)}`
        );
        let html = '';
        data.forEach(function (r, i) {
            const ok = r.cat >= r.csol;
            html += `<tr>
                <td class="text-muted">${i + 1}</td>
                <td><code>${esc(r.cod)}</code></td>
                <td>${esc(r.desc)}</td>
                <td class="text-center">${esc(r.um)}</td>
                <td class="text-center">${r.csol}</td>
                <td class="text-center ${ok ? 'text-success' : 'text-warning'} fw-bold">${r.cat}</td>
                <td class="text-end font-monospace small">${money(r.pu)}</td>
                <td class="text-end font-monospace small">${money(r.csol * r.pu)}</td>
                <td class="text-center">${badgeEstado(r.estado)}</td>
            </tr>`;
        });
        $tbody.html(html);
    }

    // ── Tabs ───────────────────────────────────────────────────
    function switchTab(name) {
        if (name === 'lista') {
            $('#cn-panel-lista').removeClass('d-none');
            $('#cn-panel-detalle').addClass('d-none');
            $('[data-cn-tab="lista"]').addClass('active');
            $('[data-cn-tab="detalle"]').removeClass('active');
        } else {
            $('#cn-panel-detalle').removeClass('d-none');
            $('#cn-panel-lista').addClass('d-none');
            $('[data-cn-tab="detalle"]').addClass('active');
            $('[data-cn-tab="lista"]').removeClass('active');
        }
    }

    // ── Init ───────────────────────────────────────────────────
    $(function () {
        if (!$('#cn-btn-buscar').length) return;

        // Carga inicial
        renderLista(DEMO_LISTA);

        // Buscar
        $('#cn-btn-buscar').on('click', function () {
            const area   = ($('#cn-area').val() || '').toLowerCase();
            const estado = $('#cn-estado').val() || '';
            const data   = DEMO_LISTA.filter(function (r) {
                return (!area   || r.area.toLowerCase().includes(area))
                    && (!estado || r.estado === estado);
            });
            renderLista(data);
        });

        // Limpiar
        $('#cn-btn-reset').on('click', function () {
            $('#cn-area, #cn-nreq').val('');
            $('#cn-estado').prop('selectedIndex', 0);
            renderLista(DEMO_LISTA);
        });

        // Tabs
        $(document).on('click', '[data-cn-tab]', function () {
            switchTab($(this).data('cn-tab'));
        });

        // Ver detalle
        $('#cn-tbody-lista').on('click', '.btn-ver-detalle', function () {
            const id   = $(this).data('id');
            const area = $(this).data('area');
            renderDetalle(id, area);
            switchTab('detalle');
        });
    });

}(jQuery));
