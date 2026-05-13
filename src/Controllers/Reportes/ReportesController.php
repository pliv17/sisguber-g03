<?php

declare(strict_types=1);

namespace App\Controllers\Reportes;

use App\Core\Request;
use App\Core\Response;

/**
 * Índice de reportes (hub con enlaces por área).
 */
final class ReportesController
{
    public function index(Request $request): void
    {
        Response::view('layouts.main', [
            'pageTitle'   => 'Reportes — Sistema de Abastecimiento',
            'contentView' => 'reportes/index',
        ]);
    }
}
