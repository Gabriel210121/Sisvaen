<?php
session_start(); // Iniciar sesión

require_once 'includes/conexion.php';

// Verificar que el usuario esté logueado y sea aprendiz
if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'aprendiz') {
    header("Location: login.php"); // Redirige al login si no cumple
    exit;
}

// Inicializar variables
$mensaje = "";
$nombre = $_SESSION['usuario'];

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_sesion = trim($_POST['codigo_sesion']);

    // Validación básica del código
    if (preg_match('/^[A-Za-z0-9]{6}$/', $codigo_sesion)) {
        // Consultar si el código existe en la tabla sesiones
        $stmt = $conexion->prepare("SELECT * FROM sesiones WHERE codigo = ?");
        $stmt->bind_param("s", $codigo_sesion);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Aquí puedes redirigir a la página de registrar asistencia o procesar directamente
            header("Location: registrar_asistencia.php?codigo=".$codigo_sesion);
            exit;
        } else {
            $mensaje = "Código de sesión no válido.";
        }
    } else {
        $mensaje = "El código debe contener exactamente 6 caracteres alfanuméricos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Aprendiz - SISVAEN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + íconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-success bg-gradient bg-opacity-75 min-vh-100 d-flex flex-column">

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <!-- Logo y nombre del sistema -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="img/logo_sena.png" alt="Logo SENA" width="35" class="me-2">
            SISVAEN - Aprendiz
        </a>
        <!-- Usuario logueado + botón salir -->
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nombre) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
        </div>
    </div>
</nav>

<!-- Contenido principal -->
<main class="flex-fill d-flex align-items-center justify-content-center py-4">
    <div class="card shadow-lg p-4 w-100" style="max-width: 450px;">
        <!-- Encabezado -->
        <div class="text-center mb-3">
            <img src="img/logo_sena.png" alt="Logo SENA" width="60">
            <h4 class="mt-3 text-success">Ingresar a una sesión</h4>
            <p class="text-muted mb-0">Ingresa el código que te proporcionó el enfermero</p>
        </div>

        <!-- Mensaje de error si existe -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <!-- Formulario para ingresar el código -->
        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="codigo_sesion" class="form-label">Código de la sesión</label>
                <input type="text" name="codigo_sesion" id="codigo_sesion" class="form-control"
                       value="<?= isset($_POST['codigo_sesion']) ? htmlspecialchars($_POST['codigo_sesion']) : '' ?>"
                       maxlength="6" pattern="[A-Za-z0-9]{6}" required>
                <div class="form-text">Debe contener exactamente 6 caracteres alfanuméricos.</div>
            </div>
            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
            </button>
        </form>
    </div>
</main>

<!-- Pie de página -->
<footer class="text-center text-white-50 small py-3 bg-dark">
    SISVAEN · Sistema de Valoración y Asistencia en Enfermería - SENA
</footer>

</body>
</html>
