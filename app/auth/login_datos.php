<?php
/**
 * login_datos.php - SisGuber G03
 * Capa de datos/lógica del proceso de autenticación.
 * Sin HTML. Devuelve variables que usa login.php.
 */

// Prevenir acceso directo
if (!defined('APP_ACCESS')) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../conexion.php';

$error   = '';
$success = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {

    $usuario = trim($_POST['username'] ?? '');
    $clave   = trim($_POST['password'] ?? '');

    if (empty($usuario) || empty($clave)) {
        $error = 'Por favor ingrese usuario y contraseña.';
    } else {
        $stmt = mysqli_prepare($conexion,
            "SELECT * FROM usuarios WHERE usuario = ? AND clave = ? LIMIT 1"
        );
        $hash = sha1($clave);
        mysqli_stmt_bind_param($stmt, 'ss', $usuario, $hash);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $fila   = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($fila) {
            session_start();
            $_SESSION['autentificado'] = 'SI';
            $_SESSION['usuario']       = $fila['usuario'];
            $_SESSION['nombres']       = $fila['nombres'];
            $_SESSION['apellidos']     = $fila['apellidos'];
            $_SESSION['nivel']         = $fila['nivel'];
            $_SESSION['verificado']    = $fila['verificado'];
            $_SESSION['oficina']       = $fila['oficina'] ?? '';

            // Si contraseña no verificada → forzar cambio
            if ($fila['verificado'] == 0) {
                header('Location: ../auth/cambiar_contrasena.php?forzado=1');
                exit;
            }
            header('Location: ../layout/index2.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}