<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Services;

use App\Modules\Maestros\Repositories\BudgetItemRepository;
use App\Modules\Maestros\Support\Pagination;
use PDOException;

final class BudgetItemService
{
    public function __construct(private readonly BudgetItemRepository $repo = new BudgetItemRepository())
    {
    }

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function list(int $year, string $q, int $page, int $perPage): array
    {
        $p = Pagination::parse($page, $perPage);

        return $this->repo->paginate($year, $q, $p['offset'], $p['per_page']);
    }

    public function find(int $id, int $year, string $code): ?array
    {
        return $this->repo->find($id, $year, $code);
    }

    /** @param array<string,mixed> $in @return array<string, list<string>> */
    public function validate(array $in): array
    {
        $e = [];
        $y = (int) ($in['year'] ?? 0);
        if ($y < 2000 || $y > 2100) {
            $e['year'][] = 'Año inválido.';
        }
        $id = (int) ($in['id'] ?? 0);
        if ($id <= 0) {
            $e['id'][] = 'ID obligatorio.';
        }
        $c = trim((string) ($in['code'] ?? ''));
        $n = trim((string) ($in['name'] ?? ''));
        if ($c === '' || strlen($c) > 40) {
            $e['code'][] = 'Código obligatorio, máx. 40.';
        }
        if ($n === '' || strlen($n) > 255) {
            $e['name'][] = 'Nombre obligatorio, máx. 255.';
        }

        return $e;
    }

    public function create(array $in): int
    {
        try {
            return $this->repo->insert(
                (int) $in['id'],
                (int) $in['year'],
                trim((string) $in['code']),
                trim((string) $in['name'])
            );
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[BudgetItemService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function update(int $oldId, int $oldYear, string $oldCode, array $in): void
    {
        try {
            $this->repo->update(
                $oldId,
                $oldYear,
                $oldCode,
                (int) $in['id'],
                (int) $in['year'],
                trim((string) $in['code']),
                trim((string) $in['name'])
            );
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[BudgetItemService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function delete(int $id, int $year, string $code): bool
    {
        return $this->repo->delete($id, $year, $code);
    }

    /** @return list<list<string>> */
    public function rowsForPdf(int $year, string $q): array
    {
        $o = [];
        foreach ($this->repo->allForReport($year, $q) as $r) {
            $o[] = [(string) $r['year'], (string) $r['code'], (string) $r['name']];
        }

        return $o;
    }
}
