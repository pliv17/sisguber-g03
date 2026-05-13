<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * HomeController — Página de bienvenida del sistema.
 *
 * Los controladores SOLO orquestan:
 *   1. Reciben Request
 *   2. Llaman a Services (lógica de negocio)
 *   3. Pasan datos a la Vista
 *
 * NUNCA pongas SQL, HTML ni lógica de negocio aquí directamente.
 */
class HomeController
{
    public function index(Request $request): void
    {
        $data = [
            'pageTitle' => 'Inicio — Sistema de Abastecimiento',
            'appEnv'    => $_ENV['APP_ENV'] ?? 'unknown',
            'phpVersion'=> PHP_VERSION,
            'dbStatus'  => $this->checkDbStatus(),
        ];

        Response::view('layouts.main', $data + ['contentView' => 'home/index']);
    }

    private function checkDbStatus(): string
    {
        try {
            \App\Core\Database::getInstance()->getConnection();
            return 'Conectado ✓';
        } catch (\Throwable) {
            return 'Sin conexión — revisa .env';
        }
    }
}
