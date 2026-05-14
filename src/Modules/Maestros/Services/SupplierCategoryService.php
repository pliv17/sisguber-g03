<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Services;

use App\Modules\Maestros\Repositories\SupplierCategoryRepository;
use App\Modules\Maestros\Support\Pagination;
use PDOException;

final class SupplierCategoryService
{
    public function __construct(private readonly SupplierCategoryRepository $repo = new SupplierCategoryRepository())
    {
    }

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function list(string $q, int $page, int $perPage): array
    {
        $p = Pagination::parse($page, $perPage);

        return $this->repo->paginate($q, $p['offset'], $p['per_page']);
    }

    public function find(int $id): ?array
    {
        return $this->repo->find($id);
    }

    /** @param array<string,mixed> $input @return array<string, list<string>> */
    public function validate(array $input): array
    {
        $e = [];
        $c = trim((string) ($input['code'] ?? ''));
        $d = trim((string) ($input['description'] ?? ''));
        if ($c === '' || strlen($c) > 20) {
            $e['code'][] = 'Código obligatorio, máx. 20.';
        }
        if ($d === '' || strlen($d) > 255) {
            $e['description'][] = 'Descripción obligatoria, máx. 255.';
        }

        return $e;
    }

    public function create(array $input): int
    {
        try {
            return $this->repo->insert(trim((string) $input['code']), trim((string) $input['description']));
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[SupplierCategoryService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function update(int $id, array $input): void
    {
        try {
            $this->repo->update($id, trim((string) $input['code']), trim((string) $input['description']));
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[SupplierCategoryService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    /** @return list<list<string>> */
    public function rowsForPdf(string $q): array
    {
        $o = [];
        foreach ($this->repo->allForReport($q) as $r) {
            $o[] = [(string) $r['code'], (string) $r['description']];
        }

        return $o;
    }
}
