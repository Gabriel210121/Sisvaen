<h4 class="mb-3">Registrar Aprendiz</h4>
<form method="POST" action="procesar_usuario.php">
    <input type="hidden" name="rol" value="aprendiz">

    <div class="mb-3">
        <label for="nombre_aprendiz" class="form-label">Nombre completo</label>
        <input type="text" name="nombre" id="nombre_aprendiz" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="correo_aprendiz" class="form-label">Correo electrónico</label>
        <input type="email" name="correo" id="correo_aprendiz" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="contrasena_aprendiz" class="form-label">Contraseña</label>
        <input type="password" name="contrasena" id="contrasena_aprendiz" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-secondary">Registrar Aprendiz</button>
</form>
