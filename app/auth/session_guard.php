<?php
/**
 * session_guard.php - SisGuber G03
 * Protege páginas que requieren autenticación.
 * Incluir al inicio de cada página protegida:
 *   require_once __DIR__ . '/../auth/session_guard.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no está autenticado → redirigir al login
if (empty($_SESSION['autentificado']) || $_SESSION['autentificado'] !== 'SI') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

// Si la contraseña no ha sido verificada → forzar cambio
if (isset($_SESSION['verificado']) && $_SESSION['verificado'] == 0) {
    $current = basename($_SERVER['PHP_SELF']);
    if ($current !== 'cambiar_contrasena.php') {
        header('Location: ' . BASE_URL . '/auth/cambiar_contrasena.php?forzado=1');
        exit;
    }
}

/**
 * Verifica si el usuario tiene un nivel de acceso específico.
 * Niveles: 'ad' = administrador, 'us' = usuario estándar
 *
 * @param  string|array $niveles  Nivel(es) permitidos
 * @return bool
 */
function tienePermiso($niveles) {
    if (!isset($_SESSION['nivel'])) return false;
    $niveles = (array) $niveles;
    return in_array($_SESSION['nivel'], $niveles);
}