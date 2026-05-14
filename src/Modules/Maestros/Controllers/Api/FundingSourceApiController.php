<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Controllers\Api;

use App\Core\JsonResponse;
use App\Core\Request;
use App\Modules\Maestros\Services\FundingSourceService;
use App\Modules\Maestros\Support\MaestroPdf;
use App\Modules\Maestros\Support\Pagination;

final class FundingSourceApiController extends BaseMaestroApiController
{
    public function __construct(private readonly FundingSourceService $service = new FundingSourceService())
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
        $id = (int) ($request->route('id') ?? 0);
        if ($id < 1) {
            JsonResponse::badRequest();
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
                JsonResponse::conflict('Código duplicado para el año.');
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
                JsonResponse::conflict();
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
        $year = $this->yearOrCurrent($request);
        MaestroPdf::stream(
            'fuentes-financiamiento.pdf',
            'Fuentes de financiamiento ' . $year,
            ['Año', 'Código', 'Nombre', 'Descripción'],
            $this->service->rowsForPdf($year, $this->searchQuery($request))
        );
    }
}
