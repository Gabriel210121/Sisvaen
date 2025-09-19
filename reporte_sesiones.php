<?php
// Iniciar sesión para manejar variables de sesión
session_start();

// Conectar a la base de datos
require_once 'includes/conexion.php';

// Validar que el usuario esté logueado y que tenga rol de administrador
// Si no cumple, se redirige al login
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Variables de filtro recibidas por GET (si no existen, se inicializan vacías)
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$enfermero_id = $_GET['enfermero_id'] ?? '';

// Consultar la lista de enfermeros para usar en el filtro
$enfermeros = $conexion->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'enfermero'");

// Construcción dinámica de condiciones del filtro
$condiciones = [];
if ($fecha_inicio && $fecha_fin) {
    $condiciones[] = "s.fecha_sesion BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}
if ($enfermero_id) {
    $condiciones[] = "u.id_usuario = '$enfermero_id'";
}
$where = count($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta principal: obtener sesiones con enfermero responsable y total de asistencias
$sql = "
SELECT 
    s.id_sesion,
    s.descripcion,
    s.fecha_sesion,
    u.nombre AS enfermero,
    COUNT(a.id_asistencia) AS total_asistencias
FROM sesiones s
INNER JOIN usuarios u ON s.id_usuario = u.id_usuario
LEFT JOIN asistencias a ON s.id_sesion = a.id_sesion
$where
GROUP BY s.id_sesion, s.descripcion, s.fecha_sesion, u.nombre
ORDER BY s.fecha_sesion DESC
";
$resultado = $conexion->query($sql);

// Preparar arrays para las gráficas
$fechas = [];
$asistencias_por_fecha = [];
$asistencias_por_enfermero = [];

// Recorrer resultados para llenar datos de las gráficas
$resultado->data_seek(0);
while ($row = $resultado->fetch_assoc()) {
    $fechas[] = $row['fecha_sesion'];
    $asistencias_por_fecha[] = $row['total_asistencias'];

    if (!isset($asistencias_por_enfermero[$row['enfermero']])) {
        $asistencias_por_enfermero[$row['enfermero']] = 0;
    }
    $asistencias_por_enfermero[$row['enfermero']] += $row['total_asistencias'];
}

// Reiniciar puntero del resultado para reutilizarlo en la tabla
$resultado->data_seek(0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Sesiones - SISVAEN</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap + Iconos -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Librería Chart.js para gráficas -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container-fluid py-4">

    <!-- Encabezado con botón de volver -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success">
            <i class="bi bi-bar-chart-fill me-2"></i>Reporte de Sesiones y Asistencias
        </h3>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card shadow-sm mb-4 rounded-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <!-- Filtro Fecha Inicio -->
                <div class="col-md-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fecha_inicio) ?>">
                </div>
                <!-- Filtro Fecha Fin -->
                <div class="col-md-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fecha_fin) ?>">
                </div>
                <!-- Filtro por enfermero -->
                <div class="col-md-3">
                    <label class="form-label">Enfermero</label>
                    <select name="enfermero_id" class="form-select">
                        <option value="">Todos</option>
                        <?php while($e = $enfermeros->fetch_assoc()): ?>
                            <option value="<?= $e['id_usuario'] ?>" <?= $enfermero_id == $e['id_usuario'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- Botón Filtrar -->
                <div class="col-md-3 text-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Gráficas -->
    <div class="row g-4 mb-4">
        <!-- Gráfico de barras por fechas -->
        <div class="col-lg-6">
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Asistencias por Fecha</h6>
                    <canvas id="graficoFechas" class="w-100" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
        <!-- Gráfico de pastel por enfermero -->
        <div class="col-lg-6">
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Asistencias por Enfermero</h6>
                    <canvas id="graficoEnfermeros" class="w-100" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de detalle de sesiones -->
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Detalle de Sesiones</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Enfermero</th>
                            <th>Total Asistencias</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['fecha_sesion']) ?></td>
                                <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                <td><?= htmlspecialchars($row['enfermero']) ?></td>
                                <td><?= $row['total_asistencias'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <!-- Mensaje si no hay registros -->
                        <?php if ($resultado->num_rows == 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay registros</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Configuración de gráficas con Chart.js -->
<script>
new Chart(document.getElementById('graficoFechas'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($fechas) ?>,
        datasets: [{
            label: 'Asistencias',
            data: <?= json_encode($asistencias_por_fecha) ?>,
            backgroundColor: '#198754'
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('graficoEnfermeros'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_keys($asistencias_por_enfermero)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($asistencias_por_enfermero)) ?>,
            backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545']
        }]
    },
    options: { responsive: true }
});
</script>

</body>
</html>
