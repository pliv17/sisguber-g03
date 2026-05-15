/**
 * public/js/logistica/comparativo.js
 * Cuadro Comparativo — lógica de cliente.
 * Requiere: jQuery, Bootstrap 5, window.showToast, window.APP_URL_BASE.
 */
(function ($) {
    'use strict';

    function esc(s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function money(v) {
        return parseFloat(v || 0).toLocaleString('es-PE', { minimumFractionDigits: 2 });
    }
    function toast(msg, type) {
        typeof window.showToast === 'function'
            ? window.showToast(msg, type || 'info')
            : alert(msg);
    }

    // ── Demo data (reemplazar con llamada API cuando el backend esté listo) ──
    const DEMO = {
        proceso : 'REQ-2026-00128',
        area    : 'Gerencia de Administración',
        proveedores: [
            { ruc: '20501234567', nombre: 'DISTRIBUCIONES EL NORTE SAC' },
            { ruc: '20609876543', nombre: 'FERRETERIA Y ACABADOS SUR SAC' },
            { ruc: '20712345678', nombre: 'SUMINISTROS INDUSTRIALES PERU SA' },
        ],
        items: [
            { cod: '00000015', desc: 'PAPEL BOND A4 75gr x 500 Hjs',    und: 'MILLAR', cant: 10, precios: [45.50, 43.00, 47.80] },
            { cod: '00000082', desc: 'TONER HP LASERJET CF217A',          und: 'UND',   cant: 6,  precios: [180.00, 195.50, 178.00] },
            { cod: '00000103', desc: 'ARCHIVADOR DE PALANCA A4 GRANDE',   und: 'UND',   cant: 50, precios: [8.50, 8.00, 9.20] },
            { cod: '00000211', desc: 'FOLDER MANILA A4',                  und: 'CIENTO',cant: 5,  precios: [12.00, 11.50, 13.00] },
            { cod: '00000310', desc: 'LAPICERO AZUL PUNTO FINO',          und: 'CAJA',  cant: 8,  precios: [18.00, 17.50, 19.00] },
        ],
    };

    function generar(data) {
        const $table = $('#cc-table');
        const $card  = $('#cc-card-cuadro');
        const $res   = $('#cc-card-resumen');

        $('#cc-label-proceso').text(data.proceso);
        $('#cc-label-items').text(data.items.length + ' ítems');

        // ── Cabecera ──────────────────────────────────────────
        let thead = '<thead class="table-light"><tr>'
            + '<th>#</th><th>Código</th><th>Descripción</th><th class="text-center">U/M</th><th class="text-center">Cant.</th>';

        data.proveedores.forEach(function (p) {
            const short = p.nombre.split(' ').slice(0, 3).join(' ');
            thead += `<th colspan="2" class="text-center table-info">${esc(short)}<br><small class="fw-normal font-monospace">${esc(p.ruc)}</small></th>`;
        });
        thead += '</tr><tr><th colspan="5"></th>';
        data.proveedores.forEach(function () {
            thead += '<th class="text-center text-muted small">P.Unit.</th>'
                   + '<th class="text-center text-muted small">Total</th>';
        });
        thead += '</tr></thead>';

        // ── Cuerpo ────────────────────────────────────────────
        const ganadores = [];
        const totalesProv = new Array(data.proveedores.length).fill(0);
        let tbody = '<tbody>';

        data.items.forEach(function (item, idx) {
            const totales = item.precios.map(function (p) { return p * item.cant; });
            const minTotal = Math.min.apply(null, totales);
            ganadores.push(data.proveedores[totales.indexOf(minTotal)]);

            let row = `<tr>
                <td class="text-muted">${idx + 1}</td>
                <td><code>${esc(item.cod)}</code></td>
                <td>${esc(item.desc)}</td>
                <td class="text-center">${esc(item.und)}</td>
                <td class="text-center font-monospace">${item.cant}</td>`;

            totales.forEach(function (t, pi) {
                const esMenor = t === minTotal;
                const cls = esMenor ? 'table-success fw-bold' : '';
                row += `<td class="text-end font-monospace ${cls}">${money(item.precios[pi])}</td>
                        <td class="text-end font-monospace ${cls}">${money(t)}</td>`;
                totalesProv[pi] += t;
            });
            row += '</tr>';
            tbody += row;
        });

        // fila totales
        const minTotalProv = Math.min.apply(null, totalesProv);
        tbody += '<tr class="table-secondary fw-bold"><td colspan="5" class="text-end small text-muted">TOTAL GENERAL</td>';
        totalesProv.forEach(function (t) {
            const cls = t === minTotalProv ? 'table-success' : '';
            tbody += `<td colspan="2" class="text-center ${cls}">S/ ${money(t)}</td>`;
        });
        tbody += '</tr></tbody>';

        $table.html(thead + tbody);
        $card.removeClass('d-none');
        $('#cc-btn-csv, #cc-btn-print').removeClass('d-none');

        // ── Resumen ───────────────────────────────────────────
        const cnt = {};
        ganadores.forEach(function (g) { cnt[g.nombre] = (cnt[g.nombre] || 0) + 1; });
        let resHtml = '<div class="d-flex flex-wrap gap-3">';
        Object.entries(cnt).forEach(function ([nom, n]) {
            resHtml += `<div class="border rounded p-3 text-center" style="min-width:180px">
                <div class="text-muted small">Mejor precio en</div>
                <div class="fs-4 fw-bold text-success">${n} ítem${n > 1 ? 's' : ''}</div>
                <div class="small text-secondary mt-1">${esc(nom)}</div>
            </div>`;
        });
        resHtml += '</div>';
        $('#cc-resumen-body').html(resHtml);
        $res.removeClass('d-none');

        toast('Cuadro comparativo generado.', 'success');
    }

    // ── Export CSV ────────────────────────────────────────────
    function exportCsv() {
        const table = document.getElementById('cc-table');
        if (!table) return;
        const rows  = [...table.querySelectorAll('tr')];
        const csv   = rows.map(function (r) {
            return [...r.querySelectorAll('th,td')]
                .map(function (c) { return '"' + c.innerText.trim().replace(/"/g, '""') + '"'; })
                .join(',');
        }).join('\n');
        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const a    = document.createElement('a');
        a.href     = URL.createObjectURL(blob);
        a.download = 'CuadroComparativo.csv';
        a.click();
    }

    $(function () {
        if (!$('#cc-btn-generar').length) return;

        $('#cc-btn-generar').on('click', function () {
            // TODO: reemplazar DEMO por llamada real a API
            // $.getJSON(apiUrl('/api/logistica/cuadro-comparativo') + '?proceso=' + ..., generar);
            generar(DEMO);
        });

        $('#cc-btn-limpiar').on('click', function () {
            ['#cc-year','#cc-proceso','#cc-area','#cc-fecha-ini','#cc-fecha-fin']
                .forEach(function (s) { $(s).val(''); });
            $('#cc-card-cuadro, #cc-card-resumen').addClass('d-none');
            $('#cc-btn-csv, #cc-btn-print').addClass('d-none');
        });

        $('#cc-btn-csv').on('click', exportCsv);
    });

}(jQuery));
