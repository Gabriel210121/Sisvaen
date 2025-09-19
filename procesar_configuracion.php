<?php

session_start();
require_once 'includes/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$password = $_POST['password'];

// Si el usuario envía una nueva contraseña
if (!empty($password)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ?, contrasena = ? WHERE id_usuario = ?");
    $stmt->bind_param("sssi", $nombre, $correo, $hash, $id_usuario);
} else {
    // Si no se envía contraseña, solo actualiza nombre y correo
    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id_usuario = ?");
    $stmt->bind_param("ssi", $nombre, $correo, $id_usuario);
}

// Ejecutar actualización
if ($stmt->execute()) {
    // Actualizar también la sesión
    $_SESSION['nombre'] = $nombre;
    $_SESSION['correo'] = $correo;

    header("Location: configuracion.php?mensaje=exito");
    exit;
} else {
    die("Error en la actualización: " . $stmt->error);
}
?>
