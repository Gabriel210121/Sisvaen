<?php
/**
 * 
 * Archivo: configuracion.php
 * Autor: Gabriel
 * Fecha: 08/09/2025
 * Descripción: Página de configuración de usuario en el sistema
 *              SISVAEN. Permite a los usuarios autenticados 
 *              actualizar su nombre, correo y contraseña. 
 *              Incluye validaciones de sesión y muestra mensajes 
 *              de confirmación de cambios.
 * 
 */

session_start();
require_once 'includes/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Obtener datos de sesión de manera segura
$rol    = isset($_SESSION['rol']) ? $_SESSION['rol'] : "rol_desconocido";
$nombre = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : "Usuario";
$correo = isset($_SESSION['correo']) ? $_SESSION['correo'] : "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Configuración</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h1 class="mb-4">⚙️ Configuración de Usuario</h1>
    <p>Hola <strong><?= htmlspecialchars($nombre) ?></strong>, aquí podrás actualizar tus datos.</p>

    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito'): ?>
      <div class="alert alert-success">✅ Cambios guardados correctamente.</div>
    <?php endif; ?>

    <div class="card shadow p-4">
      <form method="POST" action="procesar_configuracion.php">
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="nombre" name="nombre" 
                 value="<?= htmlspecialchars($nombre) ?>" required>
        </div>

        <div class="mb-3">
          <label for="correo" class="form-label">Correo</label>
          <input type="email" class="form-control" id="correo" name="correo" 
                 value="<?= htmlspecialchars($correo) ?>" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
          <input type="password" class="form-control" id="password" name="password">
        </div>

        <div class="d-flex justify-content-between">
          <a href="dashboard.php" class="btn btn-secondary">⬅️ Volver</a>
          <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
