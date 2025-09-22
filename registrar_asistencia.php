<?php
/**
 * Archivo: registrar_asistencia.php
 * Autor: Gabriel
 * Fecha: 08/09/2025
 * Descripción: Permite a los aprendices registrar su asistencia
 *              en una sesión activa dentro del sistema SISVAEN.
 */

session_start();
require_once 'includes/conexion.php';

// Habilitar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Zona horaria y charset
date_default_timezone_set('America/Bogota');
$conexion->set_charset('utf8mb4');

// Validar acceso: solo aprendices autenticados
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'aprendiz') {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$sin_sesion_activa = true;
$id_sesion = null;

// 🔹 Verificar si llega un código por GET o si ya existe una sesión guardada
if (isset($_GET['codigo'])) {
    $codigo = trim($_GET['codigo']);

    // Buscar la sesión en la BD
    $stmt = $conexion->prepare("SELECT id_sesion FROM sesiones WHERE codigo = ? AND estado = 'activa'");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $stmt->bind_result($id_sesion_encontrado);

    if ($stmt->fetch()) {
        $_SESSION['id_sesion'] = $id_sesion_encontrado;
        $id_sesion = $id_sesion_encontrado;
        $sin_sesion_activa = false;
    }
    $stmt->close();
} elseif (isset($_SESSION['id_sesion'])) {
    $id_sesion = (int) $_SESSION['id_sesion'];
    $sin_sesion_activa = false;
}

// 🔹 Procesar formulario si existe sesión activa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$sin_sesion_activa) {
    // Capturar datos del formulario
    $nombre     = trim($_POST['nombre'] ?? '');
    $apellido   = trim($_POST['apellido'] ?? '');
    $tipo_doc   = $_POST['tipo_documento'] ?? '';
    $documento  = trim($_POST['documento'] ?? '');
    $ficha      = trim($_POST['ficha'] ?? '');
    $jornada    = trim($_POST['jornada'] ?? '');
    $programa   = trim($_POST['programa_tecnologico'] ?? '');
    $genero     = $_POST['genero'] ?? null;
    $correo     = trim($_POST['correo'] ?? '');
    $telefono   = trim($_POST['telefono'] ?? '');

    // Verificar si el aprendiz ya existe
    $stmt = $conexion->prepare("SELECT id_aprendiz FROM aprendices WHERE documento = ?");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $stmt->bind_result($id_aprendiz);
    $existe = $stmt->fetch();
    $stmt->close();

    // Si no existe, insertar nuevo aprendiz
    if (!$existe) {
        $stmt = $conexion->prepare("
            INSERT INTO aprendices 
            (nombre, apellido, tipo_documento, documento, ficha, jornada, programa_tecnologico, genero, correo, telefono)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssssssss",
            $nombre, $apellido, $tipo_doc, $documento, $ficha, $jornada, $programa, $genero, $correo, $telefono
        );
        $stmt->execute();
        $id_aprendiz = $conexion->insert_id;
        $stmt->close();
    }

    // Registrar la asistencia
    $stmt = $conexion->prepare("
        INSERT INTO asistencias (id_aprendiz, id_sesion, fecha_asistencia, hora_asistencia)
        VALUES (?, ?, CURDATE(), CURTIME())
    ");
    $stmt->bind_param("ii", $id_aprendiz, $id_sesion);
    $stmt->execute();
    $stmt->close();

    $mensaje = "✅ ¡Asistencia registrada exitosamente!";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Asistencia - SISVAEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 800px;">
        <h3 class="mb-4 text-center">Formulario de Asistencia - SISVAEN</h3>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info text-center"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($sin_sesion_activa): ?>
            <div class="alert alert-warning text-center">
                ⚠️ No hay una sesión activa. Ingresa primero el código de sesión en el panel del aprendiz.
            </div>
            <div class="text-center">
                <a href="panel_aprendiz.php" class="btn btn-primary">Volver al Panel del Aprendiz</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de documento</label>
                        <select name="tipo_documento" class="form-select" required>
                            <option value="">Selecciona</option>
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="TI">Tarjeta de Identidad</option>
                            <option value="CE">Cédula de Extranjería</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Número de documento</label>
                        <input type="text" name="documento" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ficha</label>
                        <input type="text" name="ficha" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jornada</label>
                        <input type="text" name="jornada" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Programa Tecnológico</label>
                        <input type="text" name="programa_tecnologico" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Género</label>
                        <select name="genero" class="form-select">
                            <option value="" selected>Selecciona</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100">Registrar Asistencia</button>
            </form>

            <div class="mt-4 text-center">
                <a href="panel_aprendiz.php" class="btn btn-primary">Volver al Panel del Aprendiz</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
