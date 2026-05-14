<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Controllers\Api;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\JsonResponse;
use App\Core\Request;

abstract class BaseMaestroApiController
{
    protected function boot(Request $request): void
    {
        Auth::ensureDemoUserIfEnabled();
        if (!Auth::check()) {
            JsonResponse::unauthorized('Debe iniciar sesión.');
        }
    }

    protected function requireCsrf(Request $request): void
    {
        if (!Csrf::verifyFromRequest($request)) {
            JsonResponse::error(403, 'Token CSRF inválido o ausente.', 'CSRF');
        }
    }

    /**
     * @return array{page: int, per_page: int, offset: int}
     */
    protected function pagination(Request $request, int $defaultPer = 15): array
    {
        $page = (int) $request->query('page', 1);
        $per  = (int) $request->query('per_page', $defaultPer);

        return \App\Modules\Maestros\Support\Pagination::parse($page, $per, $defaultPer, 100);
    }

    protected function searchQuery(Request $request): string
    {
        return trim((string) $request->query('q', ''));
    }

    protected function yearOrCurrent(Request $request): int
    {
        $y = (int) $request->query('year', (int) date('Y'));

        return ($y >= 2000 && $y <= 2100) ? $y : (int) date('Y');
    }
}
