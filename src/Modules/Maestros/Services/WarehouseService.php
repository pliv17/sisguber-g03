<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Services;

use App\Modules\Maestros\Repositories\WarehouseRepository;
use PDOException;

final class WarehouseService
{
    public function __construct(
        private readonly WarehouseRepository $repo = new WarehouseRepository()
    ) {
    }

    /**
     * @return array{0: list<array<string, mixed>>, 1: int}
     */
    public function list(string $q, int $page, int $perPage): array
    {
        $p = \App\Modules\Maestros\Support\Pagination::parse($page, $perPage);
        return $this->repo->paginate($q, $p['offset'], $p['per_page']);
    }

    public function find(string $id): ?array
    {
        return $this->repo->find($id);
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, list<string>>
     */
    public function validateForCreate(array $input): array
    {
        $errors = [];
        $code = trim((string) ($input['code'] ?? ''));
        $name = trim((string) ($input['name'] ?? ''));
        $addr = trim((string) ($input['address'] ?? ''));
        if ($code === '' || strlen($code) > 20) {
            $errors['code'][] = 'Código obligatorio, máx. 20 caracteres.';
        }
        if ($name === '' || strlen($name) > 200) {
            $errors['name'][] = 'Nombre obligatorio, máx. 200 caracteres.';
        }
        if (strlen($addr) > 500) {
            $errors['address'][] = 'Dirección máx. 500 caracteres.';
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, list<string>>
     */
    public function validateForUpdate(array $input): array
    {
        return $this->validateForCreate($input);
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>|null fila creada o null si errores (no usar aquí)
     */
    public function create(array $input): int
    {
        $code = trim((string) ($input['code'] ?? ''));
        $name = trim((string) ($input['name'] ?? ''));
        $addr = trim((string) ($input['address'] ?? ''));

        try {
            return $this->repo->insert($code, $name, $addr);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[WarehouseService::create] ' . $e->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function update(int $id, array $input): void
    {
        $code = trim((string) ($input['code'] ?? ''));
        $name = trim((string) ($input['name'] ?? ''));
        $addr = trim((string) ($input['address'] ?? ''));
        try {
            $this->repo->update($id, $code, $name, $addr);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('DUPLICATE', 409);
            }
            error_log('[WarehouseService::update] ' . $e->getMessage());
            throw new \RuntimeException('DB_ERROR', 500);
        }
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    /**
     * @return list<array{0: string, 1: string, 2: string}>
     */
    public function rowsForPdf(string $q): array
    {
        $rows = [];
        foreach ($this->repo->allForReport($q) as $r) {
            $rows[] = [(string) $r['code'], (string) $r['name'], (string) $r['address']];
        }

        return $rows;
    }
}
