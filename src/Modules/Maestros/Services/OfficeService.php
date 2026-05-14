<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Services;

use App\Modules\Maestros\Repositories\OfficeRepository;
use App\Modules\Maestros\Support\Pagination;
use PDOException;

final class OfficeService
{
    public function __construct(private readonly OfficeRepository $repo = new OfficeRepository())
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

    /** @param array<string,mixed> $in @return array<string, list<string>> */
    public function validate(array $in): array
    {
        $e = [];
        $c = trim((string) ($in['code'] ?? ''));
        $n = trim((string) ($in['name'] ?? ''));
        $r = trim((string) ($in['responsible'] ?? ''));
        if ($c === '' || strlen($c) > 20) {
            $e['code'][] = 'Código obligatorio, máx. 20.';
        }
        if ($n === '' || strlen($n) > 200) {
            $e['name'][] = 'Nombre obligatorio, máx. 200.';
        }
        if (strlen($r) > 200) {
            $e['responsible'][] = 'Responsable máx. 200.';
        }

        return $e;
    }

    public function create(array $in): int
    {
        try {
            return $this->repo->insert(trim((string) $in['code']), trim((string) $in['name']), trim((string) ($in['responsible'] ?? '')));
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[OfficeService] ' . $ex->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function update(int $id, array $in): void
    {
        try {
            $this->repo->update($id, trim((string) $in['code']), trim((string) $in['name']), trim((string) ($in['responsible'] ?? '')));
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[OfficeService] ' . $ex->getMessage());
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
            $o[] = [(string) $r['code'], (string) $r['name'], (string) $r['responsible']];
        }

        return $o;
    }
}
