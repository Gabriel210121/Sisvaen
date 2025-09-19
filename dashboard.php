<?php
/*
 * Archivo: dashboard.php
 * Autor: Gabriel
 * Fecha: 08/09/2025
 * Descripción: Panel principal del sistema SISVAEN. 
 *              Verifica que el usuario esté logueado y 
 *              muestra diferentes opciones según su rol:
 *              - Administrador / Superadmin: gestión de usuarios, 
 *                sesiones, reportes y configuración.
 *              - Enfermero: gestión de sesiones y configuración.
 *              - Aprendiz: acceso limitado a información.
 */

session_start();

// Incluir el archivo de conexión a la base de datos
require_once 'includes/conexion.php';

// ---------------------------
// Verificar que el usuario esté logueado
// Si no existe la variable de sesión 'id_usuario', significa que no hay login
// En ese caso, redirige al archivo login.php
// ---------------------------
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit; // Detener la ejecución después de redirigir
}

// ---------------------------
// Obtener datos del usuario desde la sesión
// Si no existen, se asignan valores por defecto
// ---------------------------
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : "rol_desconocido";
$nombre = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : "Usuario";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SISVAEN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (iconos visuales) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- =======================
    Barra de navegación superior
    ======================= -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success border-bottom border-4 border-success-subtle px-4">
    <!-- Logo y nombre del sistema -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <img src="img/logo_sena.png" alt="Logo SENA" class="img-fluid" style="max-height: 40px;">
        <span class="fw-bold">SISVAEN</span>
    </a>

    <!-- Botón hamburguesa para menú colapsable en móviles -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menú de navegación -->
    <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav me-auto">
            <!-- Opciones solo para administrador y superadmin -->
            <?php if ($rol === 'administrador' || $rol === 'superadmin'): ?>
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-fill me-1"></i>Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="gestionar_usuario.php"><i class="bi bi-people-fill me-1"></i>Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="crear_sesion.php"><i class="bi bi-calendar-plus me-1"></i>Crear Sesión</a></li>
                <li class="nav-item"><a class="nav-link" href="ver_sesiones.php"><i class="bi bi-calendar-check me-1"></i>Sesiones</a></li>
                <li class="nav-item"><a class="nav-link" href="reporte_sesiones.php"><i class="bi bi-graph-up-arrow me-1"></i>Reporte</a></li>
                <li class="nav-item"><a class="nav-link" href="configuracion.php"><i class="bi bi-gear me-1"></i>Configuración</a></li>
            
            <!-- Opciones solo para enfermero -->
            <?php elseif ($rol === 'enfermero'): ?>
                <li class="nav-item"><a class="nav-link" href="crear_sesion.php"><i class="bi bi-calendar-plus me-1"></i>Crear Sesión</a></li>
                <li class="nav-item"><a class="nav-link" href="ver_sesiones.php"><i class="bi bi-calendar-check me-1"></i>Sesiones</a></li>
                <li class="nav-item"><a class="nav-link" href="configuracion.php"><i class="bi bi-gear me-1"></i>Configuración</a></li>
            <?php endif; ?>
        </ul>

        <!-- Información del usuario logueado -->
        <span class="navbar-text text-white me-3">
            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nombre) ?> (<?= htmlspecialchars($rol) ?>)
        </span>
        <!-- Botón para cerrar sesión -->
        <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
    </div>
</nav>

<!-- =======================
    Contenido principal del Dashboard
    ======================= -->
<div class="container py-5">
    <!-- Caja de bienvenida -->
    <div class="bg-success text-white text-center rounded-4 p-4 mb-5 shadow">
        <h2><i class="bi bi-speedometer2 me-2"></i>Bienvenido(a), <?= htmlspecialchars($nombre) ?></h2>
        <p class="mb-0">Estás en el panel de <strong><?= strtoupper(htmlspecialchars($rol)) ?></strong></p>
    </div>

    <!-- Opciones del panel en forma de tarjetas (cards) -->
    <div class="row g-4 justify-content-center">
        
        <!-- Gestionar usuarios (solo admin y superadmin) -->
        <?php if ($rol === 'administrador' || $rol === 'superadmin'): ?>
            <div class="col-md-4">
                <div class="card border-4 border-start border-success shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-people-fill text-success me-2"></i>Gestionar Usuarios</h5>
                        <p class="card-text">Crear, editar o eliminar administradores, enfermeros y aprendices.</p>
                        <a href="gestionar_usuario.php" class="btn btn-outline-success w-100">Administrar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Crear sesión y Ver sesiones (admin, enfermero y superadmin) -->
        <?php if ($rol === 'administrador' || $rol === 'enfermero' || $rol === 'superadmin'): ?>
            <!-- Crear sesión -->
            <div class="col-md-4">
                <div class="card border-4 border-start border-primary shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-calendar-plus text-primary me-2"></i>Crear Sesión</h5>
                        <p class="card-text">Programa nuevas sesiones de valoración para aprendices.</p>
                        <a href="crear_sesion.php" class="btn btn-outline-primary w-100">Programar</a>
                    </div>
                </div>
            </div>

            <!-- Ver sesiones -->
            <div class="col-md-4">
                <div class="card border-4 border-start border-info shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-calendar-check text-info me-2"></i>Ver Sesiones</h5>
                        <p class="card-text">Consulta el historial de sesiones generadas.</p>
                        <a href="ver_sesiones.php" class="btn btn-outline-info w-100">Ver Sesiones</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Reporte de sesiones (solo admin y superadmin) -->
        <?php if ($rol === 'administrador' || $rol === 'superadmin'): ?>
            <div class="col-md-4">
                <div class="card border-4 border-start border-warning shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-graph-up-arrow text-warning me-2"></i>Reporte de Sesiones</h5>
                        <p class="card-text">Ver estadísticas y balance de sesiones y asistencias.</p>
                        <a href="reporte_sesiones.php" class="btn btn-outline-warning w-100">Ver Reporte</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Configuración (admin, enfermero y superadmin) -->
        <?php if ($rol === 'administrador' || $rol === 'enfermero' || $rol === 'superadmin'): ?>
            <div class="col-md-4">
                <div class="card border-4 border-start border-dark shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-gear text-dark me-2"></i>Configuración</h5>
                        <p class="card-text">Actualiza tu nombre, correo y contraseña dentro del sistema.</p>
                        <a href="configuracion.php" class="btn btn-outline-dark w-100">Configurar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
