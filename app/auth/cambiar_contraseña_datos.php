<?php
/**
 * cambiar_contrasena_datos.php - SisGuber G03
 * Lógica PHP del cambio de contraseña. Sin HTML.
 */
if (!defined('APP_ACCESS')) { header('Location: login.php'); exit; }

define('BASE_URL', '..');
require_once __DIR__ . '/../auth/session_guard.php';
require_once __DIR__ . '/../conexion.php';

$error   = '';
$success = '';
$forzado = isset($_GET['forzado']) || isset($_POST['forzado']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual   = $_POST['actual']   ?? '';
    $nueva    = $_POST['nueva']    ?? '';
    $confirma = $_POST['confirma'] ?? '';
    $usuario  = $_SESSION['usuario'];

    if (empty($actual) || empty($nueva) || empty($confirma)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($nueva !== $confirma) {
        $error = 'La nueva contraseña y su confirmación no coinciden.';
    } elseif (strlen($nueva) < 6) {
        $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
    } else {
        // Verificar contraseña actual
        $stmt = mysqli_prepare($conexion,
            "SELECT usuario FROM usuarios WHERE usuario = ? AND clave = ?");
        $hashActual = sha1($actual);
        mysqli_stmt_bind_param($stmt, 'ss', $usuario, $hashActual);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) === 0) {
            $error = 'La contraseña actual es incorrecta.';
        } else {
            mysqli_stmt_close($stmt);
            // Actualizar contraseña y marcar como verificado
            $hashNueva = sha1($nueva);
            $stmt2 = mysqli_prepare($conexion,
                "UPDATE usuarios SET clave = ?, verificado = 1 WHERE usuario = ?");
            mysqli_stmt_bind_param($stmt2, 'ss', $hashNueva, $usuario);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);

            $_SESSION['verificado'] = 1;
            $success = 'Contraseña actualizada correctamente.';

            if ($forzado) {
                header('Location: ../layout/index2.php');
                exit;
            }
        }
        if (isset($stmt) && !is_bool($stmt)) mysqli_stmt_close($stmt);
    }
}