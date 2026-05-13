<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * ApiController — Endpoints JSON para Ajax y health checks.
 */
class ApiController
{
    /**
     * GET /health — Estado general del sistema.
     * Útil para monitoreo o balanceadores de carga.
     */
    public function health(Request $request): void
    {
        $dbOk = true;
        $dbMsg = 'ok';

        try {
            \App\Core\Database::getInstance()->getConnection()->query('SELECT 1');
        } catch (\Throwable $e) {
            $dbOk = false;
            $dbMsg = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN)
                ? $e->getMessage()
                : 'error';
        }

        Response::json([
            'status'    => $dbOk ? 'ok' : 'degraded',
            'timestamp' => date('c'),
            'app_env'   => $_ENV['APP_ENV'] ?? 'unknown',
            'database'  => $dbMsg,
            'php'       => PHP_VERSION,
        ]);
    }

    /**
     * GET /api/ping — Responde a la llamada Ajax de prueba desde app.js.
     */
    public function ping(Request $request): void
    {
        Response::json([
            'pong'      => true,
            'timestamp' => date('c'),
            'message'   => '¡Conexión Ajax funcionando correctamente!',
        ]);
    }
}
