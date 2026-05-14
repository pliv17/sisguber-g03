/**
 * Listado genérico de maestros — Ajax JSON, paginación, modales CRUD.
 * Requiere: jQuery, Bootstrap 5, window.APP_URL_BASE, meta[name="csrf-token"], window.showToast (app.js).
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

    $(function () {
        const $root = $('#maestro-app');
        if (!$root.length) {
            return;
        }

        let cfg;
        try {
            cfg = JSON.parse($root.attr('data-config') || '{}');
        } catch (e) {
            console.error(e);
            return;
        }

        const state = {
            page: 1,
            perPage: 15,
            q: '',
            year: new Date().getFullYear(),
            editingPk: null,
            deletePk: null,
            debounceTimer: null,
        };

        const $tbody = $('#maestro-tbody');
        const $thead = $('#maestro-thead');
        const $pag = $('#maestro-pagination');
        const $loading = $('#maestro-loading');
        const $year = $('#maestro-year');
        const $search = $('#maestro-search');
        const modalCrud = new bootstrap.Modal(document.getElementById('maestroModal'));
        const modalDel = new bootstrap.Modal(document.getElementById('maestroDeleteModal'));
        const $form = $('#maestro-form');
        const $modalBody = $('#maestro-modal-body');

        function buildHeaders() {
            let html = '';
            (cfg.columns || []).forEach(function (c) {
                html += '<th scope="col">' + esc(c.label) + '</th>';
            });
            html += '<th scope="col" class="text-end" style="width:8rem;">Acciones</th>';
            $thead.html(html);
        }

        function esc(s) {
            return String(s ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function buildQuery() {
            const params = new URLSearchParams();
            params.set('page', String(state.page));
            params.set('per_page', String(state.perPage));
            if (state.q) {
                params.set('q', state.q);
            }
            if (cfg.year) {
                params.set('year', String($year.length ? $year.val() : state.year));
            }
            return params.toString();
        }

        function loadList() {
            if (cfg.year && $year.length) {
                state.year = parseInt(String($year.val()), 10) || state.year;
            }
            $loading.show();
            const qs = buildQuery();
            $.ajax({
                url: apiUrl(cfg.api) + (qs ? '?' + qs : ''),
                method: 'GET',
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .done(function (res) {
                    if (!res || res.ok === false) {
                        toast(res && res.message ? res.message : 'Error al cargar', 'danger');
                        return;
                    }
                    renderRows(res.data || [], res.meta || {});
                })
                .fail(function (xhr) {
                    toast(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error de red', 'danger');
                })
                .always(function () {
                    $loading.hide();
                });
        }

        function pkValue(row) {
            const pk = cfg.pk || 'id';
            if (Array.isArray(pk)) {
                return pk.map(function (key) {
                    return String(row[key] ?? '');
                }).join('-');
            }
            return row[pk];
        }

        function renderRows(rows, meta) {
            if (!rows.length) {
                $tbody.html('<tr><td class="text-muted p-3" colspan="' + ((cfg.columns || []).length + 1) + '">Sin registros.</td></tr>');
            } else {
                let html = '';
                rows.forEach(function (row) {
                    html += '<tr>';
                    (cfg.columns || []).forEach(function (c) {
                        html += '<td>' + esc(row[c.key]) + '</td>';
                    });
                    const pkVal = pkValue(row);
                    html += '<td class="text-end text-nowrap">'
                        + '<button type="button" class="btn btn-sm btn-outline-primary me-1 btn-edit" data-pk="' + esc(pkVal) + '">Editar</button>'
                        + '<button type="button" class="btn btn-sm btn-outline-danger btn-del" data-pk="' + esc(pkVal) + '">Eliminar</button>'
                        + '</td></tr>';
                });
                $tbody.html(html);
            }
            renderPagination(meta);
        }

        function renderPagination(meta) {
            const total = parseInt(meta.total || 0, 10);
            const page = parseInt(meta.page || 1, 10);
            const per = parseInt(meta.per_page || state.perPage, 10);
            const pages = parseInt(meta.total_pages || 0, 10);
            state.page = page;
            state.perPage = per;
            let html = '';
            html += '<li class="page-item disabled"><span class="page-link">Total: ' + total + '</span></li>';
            html += '<li class="page-item ' + (page <= 1 ? 'disabled' : '') + '">'
                + '<a class="page-link" href="#" data-page="' + (page - 1) + '">Anterior</a></li>';
            html += '<li class="page-item disabled"><span class="page-link">' + page + ' / ' + Math.max(pages, 1) + '</span></li>';
            html += '<li class="page-item ' + (page >= pages ? 'disabled' : '') + '">'
                + '<a class="page-link" href="#" data-page="' + (page + 1) + '">Siguiente</a></li>';
            $pag.html(html);
        }

        function buildFormFields(values) {
            let html = '';
            (cfg.fields || []).forEach(function (f) {
                const name = f.name;
                const val = values && values[name] != null ? values[name] : '';
                const req = f.required ? 'required' : '';
                const ro = (cfg.pk === 'ruc' && name === 'ruc' && state.editingPk) ? 'readonly' : '';
                const max = f.maxlength ? 'maxlength="' + f.maxlength + '"' : '';
                html += '<div class="mb-3">'
                    + '<label class="form-label" for="mf-' + esc(name) + '">' + esc(f.label) + '</label>'
                    + '<input class="form-control" type="' + esc(f.type || 'text') + '" id="mf-' + esc(name) + '" name="' + esc(name) + '" value="' + esc(val) + '" ' + req + ' ' + ro + ' ' + max + '>'
                    + '<div class="invalid-feedback">Revise este campo.</div>'
                    + '</div>';
            });
            $modalBody.html(html);
        }

        function readForm() {
            const data = {};
            $modalBody.find('input').each(function () {
                const $i = $(this);
                const n = $i.attr('name');
                if (!n) {
                    return;
                }
                if ($i.attr('type') === 'number') {
                    data[n] = parseInt($i.val(), 10) || 0;
                } else {
                    data[n] = $i.val();
                }
            });
            return data;
        }

        function openCreate() {
            state.editingPk = null;
            document.getElementById('maestroModalLabel').textContent = 'Nuevo registro';
            buildFormFields({});
            $form.removeClass('was-validated');
            modalCrud.show();
        }

        function openEdit(pk) {
            state.editingPk = pk;
            document.getElementById('maestroModalLabel').textContent = 'Editar registro';
            $loading.show();
            const url = apiUrl(cfg.api + '/' + encodeURIComponent(String(pk)));
            $.ajax({ url: url, method: 'GET', dataType: 'json', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .done(function (res) {
                    if (!res || res.ok === false || !res.data) {
                        toast('No se pudo cargar el registro', 'danger');
                        return;
                    }
                    buildFormFields(res.data);
                    $form.removeClass('was-validated');
                    modalCrud.show();
                })
                .fail(function () {
                    toast('Error al cargar', 'danger');
                })
                .always(function () {
                    $loading.hide();
                });
        }

        function saveRecord() {
            if (!$form[0].checkValidity()) {
                $form.addClass('was-validated');
                return;
            }
            const body = readForm();
            body.csrf_token = csrf();
            const isCreate = state.editingPk == null;
            const url = isCreate ? apiUrl(cfg.api) : apiUrl(cfg.api + '/' + encodeURIComponent(String(state.editingPk)));
            const method = isCreate ? 'POST' : 'PUT';
            $.ajax({
                url: url,
                method: method,
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                data: JSON.stringify(body),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf(),
                },
            })
                .done(function (res) {
                    if (res && res.ok === false) {
                        if (res.errors) {
                            const msg = Object.values(res.errors).flat().join(' ');
                            toast(msg || res.message, 'danger');
                        } else {
                            toast(res.message || 'Error', 'danger');
                        }
                        return;
                    }
                    toast(res.message || 'Guardado', 'success');
                    modalCrud.hide();
                    loadList();
                })
                .fail(function (xhr) {
                    const r = xhr.responseJSON;
                    if (r && r.errors) {
                        toast(Object.values(r.errors).flat().join(' '), 'danger');
                    } else {
                        toast(r && r.message ? r.message : 'Error al guardar', 'danger');
                    }
                });
        }

        function askDelete(pk) {
            state.deletePk = pk;
            modalDel.show();
        }

        function confirmDelete() {
            const pk = state.deletePk;
            if (pk == null) {
                return;
            }
            $.ajax({
                url: apiUrl(cfg.api + '/' + encodeURIComponent(String(pk))),
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf(),
                },
            })
                .done(function (_data, _text, xhr) {
                    if (xhr.status === 204) {
                        toast('Eliminado', 'success');
                        modalDel.hide();
                        loadList();
                        return;
                    }
                })
                .fail(function (xhr) {
                    if (xhr.status === 204) {
                        toast('Eliminado', 'success');
                        modalDel.hide();
                        loadList();
                        return;
                    }
                    let r;
                    try {
                        r = xhr.responseJSON;
                    } catch (e) {
                        r = null;
                    }
                    toast(r && r.message ? r.message : 'Error al eliminar', 'danger');
                });
        }

        buildHeaders();
        loadList();

        $('#maestro-btn-new').on('click', function () {
            openCreate();
        });

        $form.on('submit', function (e) {
            e.preventDefault();
            saveRecord();
        });

        $('#maestro-btn-confirm-delete').on('click', function () {
            confirmDelete();
        });

        $tbody.on('click', '.btn-edit', function () {
            openEdit($(this).data('pk'));
        });
        $tbody.on('click', '.btn-del', function () {
            askDelete($(this).data('pk'));
        });

        $pag.on('click', 'a.page-link', function (e) {
            e.preventDefault();
            const p = parseInt($(this).data('page'), 10);
            if (!isNaN(p) && p > 0) {
                state.page = p;
                loadList();
            }
        });

        $('#maestro-btn-search').on('click', function () {
            state.q = String($search.val() || '');
            state.page = 1;
            loadList();
        });

        $search.on('keyup', function () {
            clearTimeout(state.debounceTimer);
            state.debounceTimer = setTimeout(function () {
                state.q = String($search.val() || '');
                state.page = 1;
                loadList();
            }, 450);
        });

        if ($year.length) {
            $year.on('change', function () {
                state.page = 1;
                loadList();
            });
        }

        $('#maestro-btn-pdf').on('click', function (e) {
            e.preventDefault();
            const params = new URLSearchParams();
            if (state.q) {
                params.set('q', state.q);
            }
            if (cfg.year && $year.length) {
                params.set('year', String($year.val()));
            }
            const qs = params.toString();
            window.open(apiUrl(cfg.api + '/report' + (qs ? '?' + qs : '')), '_blank');
        });

        document.getElementById('maestroModal').addEventListener('shown.bs.modal', function () {
            const first = $modalBody.find('input:visible').first();
            if (first.length) {
                first.trigger('focus');
            }
        });
    });
})(jQuery);
