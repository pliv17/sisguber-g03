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
        $id = $this->service->create($body);
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
        $this->service->update($id, $body);
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
        MaestroPdf::stream('metas-presupuestales.pdf', 'Metas presupuestales ' . $year, ['Año', 'Nombre', 'Descripción'], $this->service->rowsForPdf($year, $this->searchQuery($request)));
    }
}
