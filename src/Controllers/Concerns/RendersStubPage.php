<?php

declare(strict_types=1);

namespace App\Controllers\Concerns;

use App\Core\Request;
use App\Core\Response;

/**
 * Renderiza el layout principal con una vista “stub” hasta implementar cada módulo.
 */
trait RendersStubPage
{
    protected function renderStub(Request $request, string $pageTitle, string $heading, string $lead = ''): void
    {
        Response::view('layouts.main', [
            'pageTitle'   => $pageTitle,
            'contentView' => 'shared/stub',
            'stubHeading' => $heading,
            'stubLead'    => $lead,
        ]);
    }
}
