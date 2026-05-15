<?php

declare(strict_types=1);

namespace App\Modules\Logistica\Controllers\Api;

use App\Core\JsonResponse;
use App\Core\Request;
use App\Modules\Logistica\Services\ServiceOrderService;
use App\Modules\Maestros\Controllers\Api\BaseMaestroApiController;

/**
 * ServiceOrderApiController — API JSON para Órdenes de Servicio.
 *
 * GET  /api/logistica/ordenes-servicio
 * GET  /api/logistica/ordenes-servicio/{n}/{y}
 * POST /api/logistica/ordenes-servicio
 * POST /api/logistica/ordenes-servicio/{n}/{y}/cancelar
 */
final class ServiceOrderApiController extends BaseMaestroApiController
{
    public function __construct(
        private readonly ServiceOrderService $service = new ServiceOrderService()
    ) {}

    public function index(Request $request): void
    {
        $this->boot($request);

        $filters = [
            'year'      => (int)   $request->query('year',   (int) date('Y')),
            'q'         => (string)$request->query('q',       ''),
            'ruc'       => (string)$request->query('ruc',     ''),
            'norden'    => (string)$request->query('norden',  ''),
            'estado'    => (int)   $request->query('estado',  0),
            'fecha_ini' => (string)$request->query('fecha_ini', ''),
            'fecha_fin' => (string)$request->query('fecha_fin', ''),
        ];

        $page    = (int) $request->query('page',     1);
        $perPage = (int) $request->query('per_page', 15);
        $result  = $this->service->list($filters, $page, $perPage);

        JsonResponse::list($result['rows'], $result['meta']);
    }

    public function show(Request $request): void
    {
        $this->boot($request);

        $norden = (string) ($request->route('norden') ?? '');
        $year   = (int)    ($request->route('year')   ?? date('Y'));

        if ($norden === '') {
            JsonResponse::badRequest('Número de orden requerido.');
        }

        $row = $this->service->find($norden, $year);
        if ($row === null) {
            JsonResponse::notFound('Orden de servicio no encontrada.');
        }

        JsonResponse::item($row);
    }

    public function store(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);

        $body   = $request->isJson() ? $request->json() : $_POST;
        $body['year'] = $body['year'] ?? (int) date('Y');
        $errors = $this->service->validate($body);

        if ($errors !== []) {
            JsonResponse::validationError($errors);
        }

        try {
            $norden = $this->service->create($body);
        } catch (\RuntimeException) {
            JsonResponse::error(500, 'Error al guardar la orden de servicio.');
        }

        $row = $this->service->find($norden, (int) $body['year']);
        JsonResponse::created($row ?? []);
    }

    public function cancel(Request $request): void
    {
        $this->boot($request);
        $this->requireCsrf($request);

        $norden = (string) ($request->route('norden') ?? '');
        $year   = (int)    ($request->route('year')   ?? date('Y'));

        if ($norden === '') {
            JsonResponse::badRequest('Número de orden requerido.');
        }
        if (!$this->service->cancel($norden, $year)) {
            JsonResponse::error(409, 'No se pudo anular.');
        }

        JsonResponse::ok([], 'Orden de servicio anulada.');
    }
}
