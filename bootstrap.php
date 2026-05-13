<?php

declare(strict_types=1);

// ─────────────────────────────────────────────
// bootstrap.php — Punto de arranque de la app
// ─────────────────────────────────────────────

define('BASE_PATH', dirname(__FILE__));

// 1. Autoload de Composer (PSR-4 + vendor)
$autoload = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    die(json_encode([
        'error' => 'Dependencias no instaladas. Ejecuta: composer install'
    ]));
}
require_once $autoload;

// 2. Cargar variables de entorno desde .env
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
try {
    $dotenv->load();
    $dotenv->required([
        'DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER',
        'APP_ENV', 'APP_DEBUG', 'APP_URL',
    ]);
} catch (\Dotenv\Exception\InvalidPathException $e) {
    http_response_code(500);
    die(json_encode([
        'error' => 'Archivo .env no encontrado. Copia .env.example como .env'
    ]));
}

// 3. Zona horaria
$tz = $_ENV['APP_TIMEZONE'] ?? 'America/Lima';
date_default_timezone_set($tz);

// 4. Control de errores según entorno
$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
if ($debug) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// 5. Helpers globales (e(), asset(), url(), dd())
require_once BASE_PATH . '/src/Core/helpers.php';

// 6. Log de errores siempre activo
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/logs/app.log');
