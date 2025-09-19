<?php
/*
 * Archivo: login.php
 * Autor: Gabriel
 * Fecha: 08/09/2025
 * Descripción: Este archivo gestiona el inicio de sesión de 
 *              los usuarios en el sistema SISVAEN. Valida las 
 *              credenciales contra la base de datos, maneja 
 *              sesiones de usuario y redirige según el rol 
 *              (aprendiz o administrador/enfermero).
 */

// Iniciamos la sesión de PHP para poder usar variables de sesión
session_start();

// Incluimos el archivo de conexión a la base de datos para poder ejecutar consultas
require_once 'includes/conexion.php';

// Inicializamos la variable $mensaje vacía, que se usará para mostrar errores o notificaciones
$mensaje = "";

// Verificamos si se envió el formulario mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Guardamos y limpiamos los datos enviados por el usuario
    $correo = trim($_POST['correo']);  // Eliminamos espacios en blanco al inicio y final
    $contrasena = $_POST['contrasena'];

    // Preparamos una consulta SQL para buscar el usuario por correo
    // Se usa sentencia preparada para evitar inyecciones SQL
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo); // Asignamos el parámetro
    $stmt->execute();                // Ejecutamos la consulta
    $resultado = $stmt->get_result(); // Obtenemos el resultado

    // Verificamos si existe el usuario con ese correo
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc(); // Obtenemos los datos del usuario en un array asociativo

        // Verificamos si la contraseña ingresada coincide con la almacenada en la base de datos
        if (password_verify($contrasena, $usuario['contrasena'])) {
            // Guardamos información del usuario en variables de sesión
            $_SESSION['usuario'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['id_usuario'] = $usuario['id_usuario'];

            // Redirigimos al usuario según su rol
            if ($usuario['rol'] === 'aprendiz') {
                header("Location: panel_aprendiz.php"); // Redirige a panel de aprendiz
            } else {
                header("Location: dashboard.php");      // Redirige a dashboard para admin/enfermero
            }
            exit; // Detenemos la ejecución del script después de redirigir
        } else {
            // Si la contraseña no coincide, mostramos un mensaje de error
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        // Si el correo no está registrado, mostramos un mensaje de error
        $mensaje = "Correo no registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - SISVAEN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Incluimos estilos de Bootstrap y los íconos de Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-success bg-gradient">

<!-- Contenedor principal centrado vertical y horizontalmente -->
<div class="d-flex align-items-center justify-content-center min-vh-100 px-3">
    <!-- Card de Bootstrap que contiene el formulario -->
    <div class="card bg-white shadow-lg text-dark rounded-4 w-100" style="max-width: 420px;">
        <div class="card-body p-4">

            <!-- Logo y títulos del formulario -->
            <div class="text-center mb-3">
                <img src="img/logo_sena.png" alt="Logo SENA" class="img-fluid mb-3" style="height: 60px;">
                <h3 class="fw-bold">Iniciar Sesión</h3>
                <p class="text-muted small">SISVAEN - SENA</p>
            </div>

            <!-- Mensaje de registro exitoso, se muestra si se redirige desde el registro -->
            <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
                <div class="alert alert-success text-center">✅ Registro exitoso. Ya puedes iniciar sesión.</div>
            <?php endif; ?>

            <!-- Mensaje de error, se muestra si $mensaje tiene contenido -->
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-danger text-center"><?= $mensaje ?></div>
            <?php endif; ?>

            <!-- Formulario de inicio de sesión -->
            <form method="POST" action="">
                <!-- Campo de correo electrónico -->
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="correo" id="correo" class="form-control" required>
                    </div>
                </div>

                <!-- Campo de contraseña -->
                <div class="mb-4">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="contrasena" id="contrasena" class="form-control" required>
                    </div>
                </div>

                <!-- Botón de enviar -->
                <button type="submit" class="btn btn-success w-100 btn-lg mb-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                </button>

                <!-- Enlace de recuperación de contraseña -->
                <div class="text-center mt-2">
                    <a href="forgot_password.php" class="btn btn-link">¿Olvidaste tu contraseña?</a>
                </div>

                <!-- Enlace de registro para aprendices -->
                <div class="text-center mt-3">
                    <a href="registro_aprendiz.php" class="btn btn-link">¿Eres aprendiz? Regístrate aquí</a>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
