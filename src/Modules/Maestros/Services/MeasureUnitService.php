<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Services;

use App\Modules\Maestros\Repositories\MeasureUnitRepository;
use App\Modules\Maestros\Support\Pagination;
use PDOException;

final class MeasureUnitService
{
    public function __construct(private readonly MeasureUnitRepository $repo = new MeasureUnitRepository())
    {
    }

    /** @return array{0: list<array<string,mixed>>, 1: int} */
    public function list(string $q, int $page, int $perPage): array
    {
        $p = Pagination::parse($page, $perPage);

        return $this->repo->paginate($q, $p['offset'], $p['per_page']);
    }

    public function find(string $code): ?array
    {
        return $this->repo->find($code);
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

    public function create(array $input): string
    {
        try {
            $this->repo->insert(trim((string) $input['code']), trim((string) $input['description']));
            return trim((string) $input['code']);
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[MeasureUnitService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function update(string $oldCode, array $input): void
    {
        try {
            $this->repo->update($oldCode, trim((string) $input['code']), trim((string) $input['description']));
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[MeasureUnitService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function delete(string $code): bool
    {
        return $this->repo->delete($code);
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
