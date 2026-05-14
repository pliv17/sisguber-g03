<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Controllers\Api;

use App\Core\JsonResponse;
use App\Core\Request;
use App\Modules\Maestros\Services\WarehouseService;
use App\Modules\Maestros\Support\MaestroPdf;
use App\Modules\Maestros\Support\Pagination;

final class WarehouseApiController extends BaseMaestroApiController
{
    public function __construct(
        private readonly WarehouseService $service = new WarehouseService()
    ) {
    }

    public function index(Request $request): void
    {
        $this->boot($request);
        $p = $this->pagination($request);
        $q = $this->searchQuery($request);
        [$rows, $total] = $this->service->list($q, $p['page'], $p['per_page']);
        JsonResponse::list($rows, Pagination::meta($total, $p['page'], $p['per_page']));
    }

    public function show(Request $request): void
    {
        $this->boot($request);
        $id = $request->route('id') ?? '0';
        if ($id === '') {
            JsonResponse::badRequest('Identificador inválido.');
        }
        $row = $this->service->find($id);
        if ($row === null) {
            JsonResponse::notFound('Almacén no encontrado.');
        }
        JsonResponse::item($row);
    }

    public function store(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);
        $body = $request->isJson() ? $request->json() : $_POST;
        $errors = $this->service->validateForCreate($body);
        if ($errors !== []) {
            JsonResponse::validationError($errors);
        }
        try {
            $id = $this->service->create($body);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'DUPLICATE') {
                JsonResponse::conflict('Código duplicado.');
            }
            JsonResponse::error(500, 'Error al guardar.');
        }
        $row = $this->service->find($id);
        JsonResponse::created($row ?? ['code' => $id]);
    }

    public function update(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);
        $id = $request->route('id') ?? '0';
        if ($id === '') {
            JsonResponse::badRequest('Identificador inválido.');
        }
        $body = $request->isJson() ? $request->json() : $_POST;
        $errors = $this->service->validateForUpdate($body);
        if ($errors !== []) {
            JsonResponse::validationError($errors);
        }
        if ($this->service->find($id) === null) {
            JsonResponse::notFound('Almacén no encontrado.');
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
        $id = $request->route('id') ?? '0';
        if ($id === '') {
            JsonResponse::badRequest('Identificador inválido.');
        }
        if (!$this->service->delete($id)) {
            JsonResponse::notFound('Almacén no encontrado.');
        }
        JsonResponse::noContent();
    }

    public function report(Request $request): void
    {
        $this->boot($request);
        $q = $this->searchQuery($request);
        $rows = $this->service->rowsForPdf($q);
        MaestroPdf::stream('almacenes.pdf', 'Listado de almacenes', ['Código', 'Nombre', 'Dirección'], $rows);
    }
}
