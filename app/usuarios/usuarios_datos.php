<?php
/**
 * usuarios_datos.php - SisGuber G03
 * Capa de datos del módulo de usuarios. Solo PHP, sin HTML.
 */
if (!defined('APP_ACCESS')) { header('Location: ../auth/login.php'); exit; }
define('BASE_URL', '..');
require_once __DIR__ . '/../auth/session_guard.php';

// Solo administradores pueden gestionar usuarios
if (!tienePermiso('ad')) {
    header('Location: ../layout/index2.php?error=sinpermiso');
    exit;
}

require_once __DIR__ . '/../conexion.php';

$error   = '';
$success = '';
$accion  = $_GET['accion'] ?? 'listar';

// ---- ELIMINAR ----
if ($accion === 'eliminar' && !empty($_GET['usuario'])) {
    $usu = $_GET['usuario'];
    if ($usu === $_SESSION['usuario']) {
        $error = 'No puede eliminar su propia cuenta.';
    } else {
        $stmt = mysqli_prepare($conexion, "DELETE FROM usuarios WHERE usuario = ?");
        mysqli_stmt_bind_param($stmt, 's', $usu);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success = "Usuario '$usu' eliminado.";
    }
    $accion = 'listar';
}

// ---- GUARDAR (crear / actualizar) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usu      = trim($_POST['usuario']   ?? '');
    $nombres  = trim($_POST['nombres']   ?? '');
    $apellidos= trim($_POST['apellidos'] ?? '');
    $correo   = trim($_POST['correo']    ?? '');
    $oficina  = trim($_POST['oficina']   ?? '');
    $nivel    = $_POST['nivel']    ?? 'us';
    $editando = $_POST['editando'] ?? '';

    if (empty($usu) || empty($nombres)) {
        $error = 'Usuario y nombres son obligatorios.';
    } else {
        if ($editando) {
            // Actualizar
            $sql = "UPDATE usuarios SET nombres=?, apellidos=?, correo=?, oficina=?, nivel=? WHERE usuario=?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssss', $nombres, $apellidos, $correo, $oficina, $nivel, $editando);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            // Cambiar clave si se ingresó
            if (!empty($_POST['nueva_clave'])) {
                $hash = sha1($_POST['nueva_clave']);
                $stmt2 = mysqli_prepare($conexion, "UPDATE usuarios SET clave=?, verificado=0 WHERE usuario=?");
                mysqli_stmt_bind_param($stmt2, 'ss', $hash, $editando);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);
            }
            $success = "Usuario '$editando' actualizado.";
        } else {
            // Crear
            $clave_def = sha1('123456');
            $sql = "INSERT INTO usuarios (usuario,nombres,apellidos,correo,oficina,nivel,clave,verificado,fecha)
                    VALUES (?,?,?,?,?,?,?,0,NOW())";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssss', $usu, $nombres, $apellidos, $correo, $oficina, $nivel, $clave_def);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Usuario '$usu' creado. Contraseña por defecto: 123456";
            } else {
                $error = "Error: el usuario '$usu' ya existe.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    $accion = 'listar';
}

// ---- LISTAR ----
$busqueda = trim($_GET['q'] ?? '');
if ($busqueda) {
    $like = '%' . $busqueda . '%';
    $stmt = mysqli_prepare($conexion,
        "SELECT * FROM usuarios WHERE usuario LIKE ? OR nombres LIKE ? OR apellidos LIKE ? ORDER BY usuario");
    mysqli_stmt_bind_param($stmt, 'sss', $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $resUsuarios = mysqli_stmt_get_result($stmt);
} else {
    $resUsuarios = mysqli_query($conexion, "SELECT * FROM usuarios ORDER BY usuario ASC");
}
$totalUsuarios = mysqli_num_rows($resUsuarios);

// ---- OBTENER USUARIO PARA EDITAR ----
$usuarioEditar = null;
if ($accion === 'editar' && !empty($_GET['usuario'])) {
    $stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE usuario = ?");
    mysqli_stmt_bind_param($stmt, 's', $_GET['usuario']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $usuarioEditar = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
}