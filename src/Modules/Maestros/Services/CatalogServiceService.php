<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Services;

use App\Modules\Maestros\Repositories\CatalogServiceRepository;
use App\Modules\Maestros\Repositories\MeasureUnitRepository;
use App\Modules\Maestros\Support\Pagination;
use PDOException;

final class CatalogServiceService
{
    public function __construct(
        private readonly CatalogServiceRepository $repo = new CatalogServiceRepository(),
        private readonly MeasureUnitRepository $units = new MeasureUnitRepository()
    ) {
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

    /** @param array<string,mixed> $in @return array<string, list<string>> */
    public function validate(array $in): array
    {
        $e = [];
        $c = trim((string) ($in['code'] ?? ''));
        $n = trim((string) ($in['name'] ?? ''));
        $m = (int) ($in['measure_unit_id'] ?? 0);
        if ($c === '' || strlen($c) > 40) {
            $e['code'][] = 'Código obligatorio, máx. 40.';
        }
        if ($n === '' || strlen($n) > 255) {
            $e['name'][] = 'Nombre obligatorio, máx. 255.';
        }
        if ($m < 1 || $this->units->find($m) === null) {
            $e['measure_unit_id'][] = 'Unidad de medida inválida.';
        }

        return $e;
    }

    public function create(array $in): int
    {
        try {
            return $this->repo->insert(trim((string) $in['code']), trim((string) $in['name']), (int) $in['measure_unit_id']);
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            if (($ex->errorInfo[1] ?? null) === 1452) {
                throw new \RuntimeException('FK', 422);
            }
            error_log('[CatalogServiceService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function update(int $id, array $in): void
    {
        try {
            $this->repo->update($id, trim((string) $in['code']), trim((string) $in['name']), (int) $in['measure_unit_id']);
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            if (($ex->errorInfo[1] ?? null) === 1452) {
                throw new \RuntimeException('FK', 422);
            }
            error_log('[CatalogServiceService] ' . $ex->getMessage());
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
            $o[] = [(string) $r['code'], (string) $r['name'], (string) $r['um']];
        }

        return $o;
    }
}
