<?php
/**
 * Archivo: procesar_usuario.php
 * Descripción:
 * Este archivo procesa el formulario para registrar un nuevo usuario en el sistema SISVAEN.
 * El flujo de trabajo es el siguiente:
 *   1. Habilita la visualización de errores para depuración.
 *   2. Incluye la conexión a la base de datos.
 *   3. Verifica si la petición fue enviada por método POST.
 *   4. Captura y valida los datos enviados desde el formulario:
 *        - nombre
 *        - correo
 *        - contraseña
 *        - rol (administrador, enfermero, aprendiz)
 *   5. Si algún campo está vacío, redirige de nuevo a la vista de gestión de usuarios mostrando un error.
 *   6. Si los datos son correctos:
 *        - Se encripta la contraseña usando `password_hash()`.
 *        - Se inserta el usuario en la tabla `usuarios`.
 *   7. Dependiendo del resultado:
 *        - Si la inserción fue exitosa, redirige con un mensaje de éxito.
 *        - Si falla, redirige mostrando el error devuelto por MySQL.
 * 
 * Tabla utilizada:
 *   - usuarios (id_usuario, nombre, correo, contrasena, rol)
 * 
 * Notas:
 *   - Se usan consultas preparadas para mayor seguridad.
 *   - La contraseña nunca se guarda en texto plano, solo con hash.
 *   - Los mensajes de error/éxito se pasan como parámetros GET a `gestionar_usuario.php`.
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/conexion.php';

// Procesar solo si se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $rol = $_POST['rol'] ?? '';

    // Validar que no haya campos vacíos
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($rol)) {
        header("Location: gestionar_usuario.php?error=Todos los campos son obligatorios");
        exit;
    }

    // Encriptar contraseña
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar usuario en la BD
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $correo, $contrasena_hash, $rol);

    if ($stmt->execute()) {
        header("Location: gestionar_usuario.php?mensaje=Usuario registrado exitosamente");
    } else {
        $error = $stmt->error;
        header("Location: gestionar_usuario.php?error=" . urlencode("Error al registrar usuario: $error"));
    }
    exit;
}
?>
