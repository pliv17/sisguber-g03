<?php

declare(strict_types=1);

// ─────────────────────────────────────────────
// public/index.php — Front Controller único
// El document root de Apache/PHP apunta AQUÍ
// ─────────────────────────────────────────────

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Core\Request;
use App\Core\Router;
use App\Core\Session;

// Iniciar sesión segura (httponly, samesite)
App\Core\Session::start();

//configurar usuario y roles de demo si la opción está habilitada
if ($_ENV['AUTH_DEMO'] === 'true') {
    Session::set('user_id', 1);
    Session::set('roles', ['admin']);
}

// Instanciar request y enrutador
$request = new Request();
$router = new Router();

// Cargar definición de rutas (web + API)
require_once BASE_PATH . '/routes/web.php';
require_once BASE_PATH . '/routes/api.php';

// Despachar la petición
$router->dispatch($request);
