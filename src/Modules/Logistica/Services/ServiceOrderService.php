<?php

declare(strict_types=1);

namespace App\Modules\Logistica\Services;

use App\Modules\Logistica\Repositories\ServiceOrderRepository;
use App\Modules\Maestros\Support\Pagination;

final class ServiceOrderService
{
    public function __construct(
        private readonly ServiceOrderRepository $repo = new ServiceOrderRepository()
    ) {}

    /**
     * @param array<string,mixed> $filters
     * @return array{rows: list<array<string,mixed>>, total: int, meta: array<string,int>}
     */
    public function list(array $filters, int $page, int $perPage): array
    {
        $p = Pagination::parse($page, $perPage, 15, 100);
        [$rows, $total] = $this->repo->paginate($filters, $p['offset'], $p['per_page']);

        return [
            'rows'  => $rows,
            'total' => $total,
            'meta'  => Pagination::meta($total, $p['page'], $p['per_page']),
        ];
    }

    public function find(string $norden, int $year): ?array
    {
        return $this->repo->find($norden, $year);
    }

    /** @return array<string,list<string>> */
    public function validate(array $data): array
    {
        $errors = [];
        if (empty($data['ruc']))     $errors['ruc']     = ['El RUC del proveedor es obligatorio.'];
        if (empty($data['fecha']))   $errors['fecha']   = ['La fecha es obligatoria.'];
        if (empty($data['oficina'])) $errors['oficina'] = ['La oficina es obligatoria.'];

        return $errors;
    }

    public function create(array $data): string
    {
        return $this->repo->insert($data);
    }

    public function cancel(string $norden, int $year): bool
    {
        return $this->repo->cancel($norden, $year);
    }

    public function rowsForReport(array $filters): array
    {
        return $this->repo->allForReport($filters);
    }
}
