<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Controllers\Api;

use App\Core\JsonResponse;
use App\Core\Request;
use App\Modules\Maestros\Services\MeasureUnitService;
use App\Modules\Maestros\Support\MaestroPdf;
use App\Modules\Maestros\Support\Pagination;

final class MeasureUnitApiController extends BaseMaestroApiController
{
    public function __construct(private readonly MeasureUnitService $service = new MeasureUnitService())
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
        $id = (int) ($request->route('id') ?? 0);
        if ($id < 1) {
            JsonResponse::badRequest('Identificador inválido.');
        }
        $row = $this->service->find($id);
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
        $err = $this->service->validate($body);
        if ($err !== []) {
            JsonResponse::validationError($err);
        }
        try {
            $id = $this->service->create($body);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'DUPLICATE') {
                JsonResponse::conflict('Código duplicado.');
            }
            JsonResponse::error(500, 'Error al guardar.');
        }
        JsonResponse::created($this->service->find($id) ?? ['id' => $id]);
    }

    public function update(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);
        $id = (int) ($request->route('id') ?? 0);
        $body = $request->isJson() ? $request->json() : $_POST;
        $err = $this->service->validate($body);
        if ($err !== []) {
            JsonResponse::validationError($err);
        }
        if ($this->service->find($id) === null) {
            JsonResponse::notFound();
        }
        try {
            $this->service->update($id, $body);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'DUPLICATE') {
                JsonResponse::conflict('Código duplicado.');
            }
            JsonResponse::error(500, 'Error al actualizar.');
        }
        JsonResponse::item($this->service->find($id) ?? []);
    }

    public function destroy(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);
        $id = (int) ($request->route('id') ?? 0);
        if ($id < 1) {
            JsonResponse::badRequest('Identificador inválido.');
        }
        if (!$this->service->delete($id)) {
            JsonResponse::notFound();
        }
        JsonResponse::noContent();
    }

    public function report(Request $request): void
    {
        $this->boot($request);
        MaestroPdf::stream('unidades-medida.pdf', 'Unidades de medida', ['Código', 'Descripción'], $this->service->rowsForPdf($this->searchQuery($request)));
    }
}
