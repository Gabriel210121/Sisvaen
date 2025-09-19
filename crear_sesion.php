<?php
/**
 * Archivo: crear_sesion.php
 * Autor: Gabriel
 * Fecha: 08/09/2025
 * Descripción: Este archivo permite a los roles autorizados
 *              (enfermero y administrador) crear nuevas 
 *              sesiones de valoración en SISVAEN.
 *
 * Flujo de trabajo:
 *   1. Verifica que el usuario tenga sesión activa y rol válido.
 *   2. Recibe los datos del formulario (título, fecha, hora, descripción).
 *   3. Genera un código único de sesión.
 *   4. Inserta la sesión en la base de datos.
 *   5. Muestra mensajes de éxito o error.
 *
 * Notas:
 *   - Se valida que todos los campos estén completos.
 *   - El código de la sesión es único y en mayúsculas.
 *   - Solo usuarios con rol "enfermero" o "administrador" 
 *     pueden acceder a este archivo.
 */

session_start();
require_once 'includes/conexion.php';

// ---------------- VALIDAR ROL AUTORIZADO ----------------
// Solo "enfermero" y "administrador" pueden acceder a este archivo.
// Si no tiene sesión activa o el rol no corresponde, redirige al login.
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'enfermero' && $_SESSION['rol'] !== 'administrador')) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$clase_alerta = "info"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario
    $titulo      = trim($_POST['titulo']);
    $fecha       = $_POST['fecha_sesion'];
    $hora        = $_POST['hora_sesion'];
    $descripcion = trim($_POST['descripcion']);
    $id_usuario  = $_SESSION['id_usuario']; // Usuario creador (enfermero o admin)

    // Validar que todos los campos estén completos
    if ($titulo && $fecha && $hora && $descripcion) {
        // Generar un código único de 6 caracteres en mayúsculas
        $codigo = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

        // Preparar la consulta SQL para insertar la sesión
        $stmt = $conexion->prepare("INSERT INTO sesiones (codigo, titulo, descripcion, fecha_sesion, hora_sesion, id_usuario) VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            // Asociar parámetros a la consulta
            $stmt->bind_param("sssssi", $codigo, $titulo, $descripcion, $fecha, $hora, $id_usuario);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Éxito en la creación
                $mensaje = "✅ Sesión creada exitosamente. <br><strong>Código generado: $codigo</strong>";
                $clase_alerta = "success";
            } else {
                // Error en la ejecución
                $mensaje = "❌ Error al guardar: " . $stmt->error;
                $clase_alerta = "danger";
            }
            $stmt->close();
        } else {
            // Error en la preparación de la consulta
            $mensaje = "❌ Error en la consulta: " . $conexion->error;
            $clase_alerta = "danger";
        }
    } else {
        // Faltan campos obligatorios
        $mensaje = "⚠️ Por favor completa todos los campos.";
        $clase_alerta = "warning";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Sesión - SISVAEN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS y Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow p-4">
                <!-- Título del formulario -->
                <h3 class="text-center text-success mb-4">
                    <i class="bi bi-calendar-plus-fill me-2"></i>Crear Nueva Sesión
                </h3>

                <!-- Mostrar mensaje de éxito o error -->
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $clase_alerta ?> text-center">
                        <?= $mensaje ?>
                    </div>
                <?php endif; ?>

                <!-- Formulario de creación de sesión -->
                <form method="POST">
                    <!-- Campo título -->
                    <div class="mb-3">
                        <label for="titulo" class="form-label">
                            <i class="bi bi-type me-1"></i>Título
                        </label>
                        <input type="text" name="titulo" id="titulo" class="form-control" required>
                    </div>

                    <!-- Campo descripción -->
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="bi bi-card-text me-1"></i>Descripción
                        </label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
                    </div>

                    <!-- Campo fecha -->
                    <div class="mb-3">
                        <label for="fecha_sesion" class="form-label">
                            <i class="bi bi-calendar-event me-1"></i>Fecha
                        </label>
                        <input type="date" name="fecha_sesion" id="fecha_sesion" class="form-control" required>
                    </div>

                    <!-- Campo hora -->
                    <div class="mb-3">
                        <label for="hora_sesion" class="form-label">
                            <i class="bi bi-clock me-1"></i>Hora
                        </label>
                        <input type="time" name="hora_sesion" id="hora_sesion" class="form-control" required>
                    </div>

                    <!-- Botón enviar -->
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-1"></i>Crear Sesión
                    </button>

                    <!-- Botón volver al panel -->
                    <a href="dashboard.php" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left-circle me-1"></i>Volver al Panel
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
