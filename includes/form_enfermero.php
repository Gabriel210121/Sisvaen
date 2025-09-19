<h4 class="mb-3">Registrar Enfermero</h4>
<form method="POST" action="procesar_usuario.php">
    <input type="hidden" name="rol" value="enfermero">

    <div class="mb-3">
        <label for="nombre_enfermero" class="form-label">Nombre completo</label>
        <input type="text" name="nombre" id="nombre_enfermero" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="correo_enfermero" class="form-label">Correo electrónico</label>
        <input type="email" name="correo" id="correo_enfermero" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="contrasena_enfermero" class="form-label">Contraseña</label>
        <input type="password" name="contrasena" id="contrasena_enfermero" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Registrar Enfermero</button>
</form>
