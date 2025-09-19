<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SISVAEN - Inicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Importa Bootstrap 5 (para estilos y diseño responsivo) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Importa Bootstrap Icons (colección de íconos listos para usar) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light"> <!-- Fondo claro para toda la página -->

    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <!-- Logo y nombre del sistema en la barra -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="img/logo_sena.png" alt="Logo SENA" height="40" class="me-2">
                <span class="fw-semibold fs-5">SISVAEN</span>
            </a>
        </div>
    </nav>

    <!-- Sección principal centrada en la pantalla -->
    <section class="d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">

                    <!-- Título de bienvenida -->
                    <h1 class="display-5 fw-bold text-success mb-2">Bienvenido a Sisvaen</h1>
                    <p class="lead text-muted mb-4">Sistema de Valoración y Asistencia en Enfermería</p>

                    <!-- Tarjeta de acceso al sistema -->
                    <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                        <!-- Encabezado de la tarjeta -->
                        <h4 class="mb-4 text-success">
                            <i class="bi bi-door-open-fill me-2"></i>Accede al sistema
                        </h4>

                        <!-- Botón para iniciar sesión -->
                        <a href="login.php" class="btn btn-success btn-lg w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                        </a>

                        <!-- Texto explicativo para usuarios nuevos -->
                        <p class="text-muted mt-3 mb-2">¿Eres aprendiz nuevo?</p>

                        <!-- Botón para registrarse como nuevo aprendiz -->
                        <a href="registro_aprendiz.php" class="btn btn-outline-success w-100">
                            <i class="bi bi-person-plus me-1"></i> Registrarse como Aprendiz
                        </a>
                    </div>

                    <!-- Pie de página con el año dinámico (PHP) -->
                    <p class="mt-4 text-muted small mb-0">
                        &copy; <?= date('Y') ?> SISVAEN - Desarrollado para el SENA
                    </p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
