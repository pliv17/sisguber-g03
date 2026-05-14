<?php

declare(strict_types=1);

namespace App\Modules\Maestros\Support;

final class Pagination
{
    /**
     * @return array{page: int, per_page: int, offset: int}
     */
    public static function parse(int $page, int $perPage, int $defaultPer = 15, int $maxPer = 100): array
    {
        $page = max(1, $page);
        $per  = $perPage > 0 ? $perPage : $defaultPer;
        $per  = min($maxPer, max(1, $per));

        return [
            'page'     => $page,
            'per_page' => $per,
            'offset'   => ($page - 1) * $per,
        ];
    }

    /**
     * @return array{total: int, page: int, per_page: int, total_pages: int}
     */
    public static function meta(int $total, int $page, int $perPage): array
    {
        $pages = $perPage > 0 ? (int) ceil($total / $perPage) : 0;

        return [
            'total'        => $total,
            'page'         => $page,
            'per_page'     => $perPage,
            'total_pages'  => $pages,
        ];
    }
}
