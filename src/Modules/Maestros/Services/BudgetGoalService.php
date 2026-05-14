<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Services;

use App\Modules\Maestros\Repositories\BudgetGoalRepository;
use App\Modules\Maestros\Support\Pagination;
use PDOException;

final class BudgetGoalService
{
    public function __construct(private readonly BudgetGoalRepository $repo = new BudgetGoalRepository())
    {
    }

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function list(int $year, string $q, int $page, int $perPage): array
    {
        $p = Pagination::parse($page, $perPage);

        return $this->repo->paginate($year, $q, $p['offset'], $p['per_page']);
    }

    public function find(int $id): ?array
    {
        return $this->repo->find($id);
    }

    /** @param array<string,mixed> $in @return array<string, list<string>> */
    public function validate(array $in): array
    {
        $e = [];
        $y = (int) ($in['year'] ?? 0);
        if ($y < 2000 || $y > 2100) {
            $e['year'][] = 'Año inválido.';
        }
        $n = trim((string) ($in['name'] ?? ''));
        if ($n === '' || strlen($n) > 255) {
            $e['name'][] = 'Nombre obligatorio, máx. 255.';
        }
        $d = $in['description'] ?? null;
        if ($d !== null && is_string($d) && strlen($d) > 500) {
            $e['description'][] = 'Descripción máx. 500.';
        }

        return $e;
    }

    public function create(array $in): int
    {
        try {
            return $this->repo->insert(
                (int) $in['year'],
                trim((string) $in['name']),
                isset($in['description']) ? trim((string) $in['description']) : null
            );
        } catch (PDOException $ex) {
            error_log('[BudgetGoalService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function update(int $id, array $in): void
    {
        $this->repo->update(
            $id,
            (int) $in['year'],
            trim((string) $in['name']),
            isset($in['description']) ? trim((string) $in['description']) : null
        );
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    /** @return list<list<string>> */
    public function rowsForPdf(int $year, string $q): array
    {
        $o = [];
        foreach ($this->repo->allForReport($year, $q) as $r) {
            $o[] = [(string) $r['year'], (string) $r['name'], (string) ($r['description'] ?? '')];
        }

        return $o;
    }
}
