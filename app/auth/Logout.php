<?php
/**
 * logout.php - SisGuber G03
 * Cierra la sesión del usuario y redirige al login.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destruir todos los datos de sesión
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

header('Location: login.php?logout=1');
exit;
