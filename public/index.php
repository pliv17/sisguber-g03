<?php

declare(strict_types=1);

// ─────────────────────────────────────────────
// public/index.php — Front Controller único
// El document root de Apache/PHP apunta AQUÍ
// ─────────────────────────────────────────────

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Core\Request;
use App\Core\Router;

// Iniciar sesión segura (httponly, samesite)
App\Core\Session::start();

// Instanciar request y enrutador
$request = new Request();
$router  = new Router();

// Cargar definición de rutas
require_once BASE_PATH . '/routes/web.php';

// Despachar la petición
$router->dispatch($request);
