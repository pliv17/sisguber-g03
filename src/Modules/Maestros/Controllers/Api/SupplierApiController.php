<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Controllers\Api;

use App\Core\JsonResponse;
use App\Core\Request;
use App\Modules\Maestros\Services\SupplierService;
use App\Modules\Maestros\Support\MaestroPdf;
use App\Modules\Maestros\Support\Pagination;
use App\Modules\Maestros\Support\RucValidator;

final class SupplierApiController extends BaseMaestroApiController
{
    public function __construct(private readonly SupplierService $service = new SupplierService())
    {
    }

    public function index(Request $request): void
    {
        $this->boot($request);
        $p = $this->pagination($request);
        [$rows, $total] = $this->service->list($this->searchQuery($request), $p['page'], $p['per_page']);
        JsonResponse::list($rows, Pagination::meta($total, $p['page'], $p['per_page']));
    }

    public function show(Request $request): void
    {
        $this->boot($request);
        $ruc = (string) ($request->route('ruc') ?? '');
        if ($ruc === '') {
            JsonResponse::badRequest();
        }
        $row = $this->service->find($ruc);
        if ($row === null) {
            JsonResponse::notFound();
        }
        JsonResponse::item($row);
    }

    public function store(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);
        $body = $request->isJson() ? $request->json() : $_POST;
        $err = $this->service->validate($body, true);
        if ($err !== []) {
            JsonResponse::validationError($err);
        }
        try {
            $this->service->create($body);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'DUPLICATE') {
                JsonResponse::conflict('RUC ya registrado.');
            }
            JsonResponse::error(500, 'Error al guardar.');
        }
        JsonResponse::created($this->service->find(trim((string) $body['ruc'])) ?? []);
    }

    public function update(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);
        $ruc = (string) ($request->route('ruc') ?? '');
        $body = $request->isJson() ? $request->json() : $_POST;
        $err = $this->service->validate($body + ['ruc' => $ruc], false);
        if ($err !== []) {
            JsonResponse::validationError($err);
        }
        if ($this->service->find($ruc) === null) {
            JsonResponse::notFound();
        }
        try {
            $this->service->update($ruc, $body);
        } catch (\RuntimeException) {
            JsonResponse::error(500, 'Error al actualizar.');
        }
        JsonResponse::item($this->service->find($ruc) ?? []);
    }

    public function destroy(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);
        $ruc = trim((string) ($request->route('ruc') ?? ''));
        if ($ruc === '' || !RucValidator::isValid($ruc)) {
            JsonResponse::badRequest('RUC inválido.');
        }
        if (!$this->service->delete($ruc)) {
            JsonResponse::notFound();
        }
        JsonResponse::noContent();
    }

    public function report(Request $request): void
    {
        $this->boot($request);
        MaestroPdf::stream('proveedores.pdf', 'Proveedores', ['RUC', 'Nombre', 'Dirección'], $this->service->rowsForPdf($this->searchQuery($request)));
    }
}
