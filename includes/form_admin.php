<h4 class="mb-3">Registrar Administrador</h4>
<form method="POST" action="procesar_usuario.php">
    <input type="hidden" name="rol" value="administrador">

    <div class="mb-3">
        <label for="nombre_admin" class="form-label">Nombre completo</label>
        <input type="text" name="nombre" id="nombre_admin" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="correo_admin" class="form-label">Correo electrónico</label>
        <input type="email" name="correo" id="correo_admin" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="contrasena_admin" class="form-label">Contraseña</label>
        <input type="password" name="contrasena" id="contrasena_admin" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Registrar Administrador</button>
</form>
