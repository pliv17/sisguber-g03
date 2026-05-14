<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Controllers\Api;

use App\Core\JsonResponse;
use App\Core\Request;
use App\Modules\Maestros\Services\BudgetGoalService;
use App\Modules\Maestros\Support\MaestroPdf;
use App\Modules\Maestros\Support\Pagination;

final class BudgetGoalApiController extends BaseMaestroApiController
{
    public function __construct(private readonly BudgetGoalService $service = new BudgetGoalService())
    {
    }

    public function index(Request $request): void
    {
        $this->boot($request);
        $year = $this->yearOrCurrent($request);
        $p = $this->pagination($request);
        [$rows, $total] = $this->service->list($year, $this->searchQuery($request), $p['page'], $p['per_page']);
        JsonResponse::list($rows, Pagination::meta($total, $p['page'], $p['per_page']) + ['year' => $year]);
    }

    public function show(Request $request): void
    {
        $this->boot($request);
        $id = $request->route('id') ?? '';
        $parts = $id === '' ? [] : explode('-', $id, 3);
        if (count($parts) !== 3) {
            JsonResponse::badRequest();
        }
        [$pkId, $yearStr, $code] = $parts;
        $year = (int) $yearStr;
        $idValue = (int) $pkId;
        if ($idValue <= 0 || $year < 2000) {
            JsonResponse::badRequest();
        }
        $row = $this->service->find($idValue, $year, $code);
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
            $this->service->create($body);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'DUPLICATE') {
                JsonResponse::conflict('Código duplicado.');
            }
            JsonResponse::error(500, 'Error al guardar.');
        }
        $id = (int) $body['id'];
        $year = (int) $body['year'];
        $code = trim((string) $body['code']);
        JsonResponse::created($this->service->find($id, $year, $code) ?? ['id' => $id, 'year' => $year, 'code' => $code, 'name' => $body['name'], 'description' => $body['description'] ?? null]);
    }

    public function update(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);
        $id = $request->route('id') ?? '';
        $parts = $id === '' ? [] : explode('-', $id, 3);
        if (count($parts) !== 3) {
            JsonResponse::badRequest();
        }
        [$oldIdStr, $oldYearStr, $oldCode] = $parts;
        $oldId = (int) $oldIdStr;
        $oldYear = (int) $oldYearStr;
        if ($oldId <= 0 || $oldYear < 2000) {
            JsonResponse::badRequest();
        }
        $body = $request->isJson() ? $request->json() : $_POST;
        $err = $this->service->validate($body);
        if ($err !== []) {
            JsonResponse::validationError($err);
        }
        if ($this->service->find($oldId, $oldYear, $oldCode) === null) {
            JsonResponse::notFound();
        }
        try {
            $this->service->update($oldId, $oldYear, $oldCode, $body);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'DUPLICATE') {
                JsonResponse::conflict('Código duplicado.');
            }
            JsonResponse::error(500, 'Error al actualizar.');
        }
        $newId = (int) $body['id'];
        $newYear = (int) $body['year'];
        $newCode = trim((string) $body['code']);
        JsonResponse::item($this->service->find($newId, $newYear, $newCode) ?? []);
    }

    public function destroy(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);
        $id = $request->route('id') ?? '';
        $parts = $id === '' ? [] : explode('-', $id, 3);
        if (count($parts) !== 3) {
            JsonResponse::badRequest('Identificador inválido.');
        }
        [$pkId, $yearStr, $code] = $parts;
        $year = (int) $yearStr;
        $idValue = (int) $pkId;
        if ($idValue <= 0 || $year < 2000) {
            JsonResponse::badRequest();
        }
        if (!$this->service->delete($idValue, $year, $code)) {
            JsonResponse::notFound();
        }
        JsonResponse::noContent();
    }

    public function report(Request $request): void
    {
        $this->boot($request);
        $year = $this->yearOrCurrent($request);
        MaestroPdf::stream('metas-presupuestales.pdf', 'Metas presupuestales ' . $year, ['Año', 'Nombre', 'Descripción'], $this->service->rowsForPdf($year, $this->searchQuery($request)));
    }
}
