<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Services;

use App\Modules\Maestros\Repositories\SupplierRepository;
use App\Modules\Maestros\Support\Pagination;
use App\Modules\Maestros\Support\RucValidator;
use PDOException;

final class SupplierService
{
    public function __construct(private readonly SupplierRepository $repo = new SupplierRepository())
    {
    }

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function list(string $q, int $page, int $perPage): array
    {
        $p = Pagination::parse($page, $perPage);

        return $this->repo->paginate($q, $p['offset'], $p['per_page']);
    }

    public function find(string $ruc): ?array
    {
        return $this->repo->findByRuc($ruc);
    }

    /** @param array<string,mixed> $input @return array<string, list<string>> */
    public function validate(array $input, bool $isCreate): array
    {
        $e = [];
        $ruc = trim((string) ($input['ruc'] ?? ''));
        $name = trim((string) ($input['name'] ?? ''));
        $addr = trim((string) ($input['address'] ?? ''));
        if ($isCreate && !RucValidator::isValid($ruc)) {
            $e['ruc'][] = 'RUC inválido: debe tener 11 dígitos numéricos.';
        }
        if (!$isCreate && $ruc === '') {
            $e['ruc'][] = 'RUC requerido.';
        }
        if ($name === '' || strlen($name) > 255) {
            $e['name'][] = 'Nombre obligatorio, máx. 255.';
        }
        if (strlen($addr) > 500) {
            $e['address'][] = 'Dirección máx. 500.';
        }

        return $e;
    }

    public function create(array $input): void
    {
        $ruc = trim((string) $input['ruc']);
        try {
            $this->repo->insert($ruc, trim((string) $input['name']), trim((string) ($input['address'] ?? '')));
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[SupplierService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function update(string $ruc, array $input): void
    {
        try {
            $this->repo->update($ruc, trim((string) $input['name']), trim((string) ($input['address'] ?? '')));
        } catch (PDOException $ex) {
            error_log('[SupplierService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function delete(string $ruc): bool
    {
        return $this->repo->delete($ruc);
    }

    /** @return list<list<string>> */
    public function rowsForPdf(string $q): array
    {
        $o = [];
        foreach ($this->repo->allForReport($q) as $r) {
            $o[] = [(string) $r['ruc'], (string) $r['name'], (string) $r['address']];
        }

        return $o;
    }
}
