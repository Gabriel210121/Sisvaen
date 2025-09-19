<?php
// 1. Conectar a la base de datos
require_once 'includes/conexion.php';

// 2. Inicializar variable de mensajes para mostrar alertas al usuario
$mensaje = "";

// 3. Procesar el formulario si la petición es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3.1 Capturar y limpiar los datos ingresados
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    // 3.2 Validar que no haya campos vacíos
    if (empty($nombre) || empty($correo) || empty($contrasena)) {
        $mensaje = "⚠️ Por favor, completa todos los campos.";
    } else {
        // 3.3 Encriptar la contraseña con password_hash()
        $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

        // 3.4 Asignar rol predeterminado para este registro
        $rol = 'aprendiz';

        // 3.5 Preparar la sentencia SQL con parámetros para evitar inyección
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $correo, $contrasenaHash, $rol);

        // 3.6 Ejecutar la inserción en la base de datos
        if ($stmt->execute()) {
            // 3.7 Si es exitoso, redirigir al login con un mensaje de éxito
            header("Location: login.php?registro=exitoso");
            exit;
        } else {
            // 3.8 Si falla, mostrar mensaje de error
            $mensaje = "❌ Error al registrar. Verifica si el correo ya está en uso.";
        }

        // 3.9 Cerrar statement
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Aprendiz - SISVAEN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-success d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<!-- Contenedor principal del formulario -->
<div class="bg-white rounded-4 shadow p-4 w-100" style="max-width: 500px;">
    <!-- Encabezado -->
    <div class="text-center">
        <img src="img/logo_sena.png" alt="Logo SENA" class="mb-3" style="width: 80px;">
        <h3 class="text-dark mb-2">Registro de Aprendiz</h3>
        <p class="text-secondary">Crea tu cuenta para registrar asistencias</p>
    </div>

    <!-- Mostrar mensajes si existen -->
    <?php if ($mensaje): ?>
        <div class="alert alert-warning text-center"><?= $mensaje ?></div>
    <?php endif; ?>

    <!-- Formulario de registro -->
    <form method="POST" action="">
        <!-- Campo Nombre -->
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Ana Pérez" required>
        </div>

        <!-- Campo Correo -->
        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" name="correo" id="correo" class="form-control" placeholder="Ej: ana@email.com" required>
        </div>

        <!-- Campo Contraseña -->
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Mínimo 6 caracteres" required>
        </div>

        <!-- Botón Registrar -->
        <button type="submit" class="btn btn-success w-100">
            <i class="bi bi-check-circle-fill"></i> Registrarse
        </button>

        <!-- Link para volver al login -->
        <div class="text-center mt-3">
            <a href="login.php" class="text-success text-decoration-none">
                ¿Ya tienes cuenta? <u>Inicia sesión</u>
            </a>
        </div>
    </form>
</div>
</body>
</html>
