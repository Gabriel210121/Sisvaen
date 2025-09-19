<?php
/**
 * Archivo: gestionar_usuario.php
 * Autor: Gabriel
 * Fecha: 08/09/2025
 * Descripción: Interfaz principal para la gestión de usuarios
 * dentro de SISVAEN. Permite al administrador
 * crear y administrar cuentas de:
 * - Administradores
 *- Enfermeros
 *- Aprendices
 *
 * Flujo de trabajo:
 *   1. Valida que el usuario logueado tenga rol "administrador".
 *   2. Captura mensajes de éxito o error enviados por la URL.
 *   3. Muestra un panel con pestañas para cada tipo de usuario.
 *   4. Incluye formularios externos desde la carpeta `includes/`
 *      para registrar usuarios según su rol.
 * Notas:
 *   - Solo accesible para administradores.
 *   - Integra Bootstrap e iconos para una mejor interfaz.
 *   - Los formularios están modularizados en archivos separados.
 
 */
session_start();
require_once 'includes/conexion.php';

// Verificamos si el usuario está logueado y es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Capturamos mensajes desde la URL (ej: ?mensaje=ok o ?error=fallo)
$mensaje = $_GET['mensaje'] ?? '';
$error   = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios - SISVAEN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100">

<div class="container py-5">

    <!-- Botón volver -->
    <div class="text-end mb-4">
        <a href="dashboard.php" class="btn btn-outline-success">
            <i class="bi bi-arrow-left-circle"></i> Volver al Panel
        </a>
    </div>

    <!-- Título -->
    <h2 class="text-center text-success mb-4">
        <i class="bi bi-people-fill me-2"></i> Gestión de Usuarios
    </h2>

    <!-- Mensajes de éxito o error -->
    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Pestañas -->
    <ul class="nav nav-tabs justify-content-center mb-4" id="userTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">
                <i class="bi bi-shield-lock-fill me-1"></i> Administrador
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="enfermero-tab" data-bs-toggle="tab" data-bs-target="#enfermero" type="button" role="tab">
                <i class="bi bi-person-badge-fill me-1"></i> Enfermero
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aprendiz-tab" data-bs-toggle="tab" data-bs-target="#aprendiz" type="button" role="tab">
                <i class="bi bi-person-fill me-1"></i> Aprendiz
            </button>
        </li>
    </ul>

    <!-- Contenido de cada pestaña -->
    <div class="tab-content border rounded bg-white p-4 shadow-sm" id="userTabsContent">
        <div class="tab-pane fade show active" id="admin" role="tabpanel">
            <?php include 'includes/form_admin.php'; ?>
        </div>
        <div class="tab-pane fade" id="enfermero" role="tabpanel">
            <?php include 'includes/form_enfermero.php'; ?>
        </div>
        <div class="tab-pane fade" id="aprendiz" role="tabpanel">
            <?php include 'includes/form_aprendiz.php'; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
