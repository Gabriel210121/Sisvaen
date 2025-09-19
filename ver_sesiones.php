<?php
// Iniciar sesión para manejar variables de sesión
session_start();

// Conectar a la base de datos
require_once 'includes/conexion.php';

// Validar que el usuario esté logueado y que tenga rol válido (administrador o enfermero)
// Si no cumple, se redirige al login
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['administrador','enfermero'])) {
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ver Sesiones - SISVAEN</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap + Iconos -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body { background-color: #f8f9fa; }
    .collapse-row td { background: #f1f1f1; }
</style>
</head>
<body>

<div class="container py-4">
    <!-- Encabezado con botón de volver -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success"><i class="bi bi-calendar-check me-2"></i>Listado de Sesiones</h3>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card shadow-sm mb-4">
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
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-search me-1"></i> Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de sesiones -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Enfermero</th>
                        <th>Total Asistencias</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $resultado->fetch_assoc()): ?>
                    <!-- Fila principal de la sesión -->
                    <tr>
                        <td><?= htmlspecialchars($row['fecha_sesion']) ?></td>
                        <td><?= htmlspecialchars($row['descripcion']) ?></td>
                        <td><?= htmlspecialchars($row['enfermero']) ?></td>
                        <td><?= $row['total_asistencias'] ?></td>
                        <td>
                            <!-- Botón para ver asistentes -->
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#asistentes<?= $row['id_sesion'] ?>">
                                <i class="bi bi-people-fill me-1"></i> Ver Asistentes
                            </button>
                            <!-- Botón exportar a Excel -->
                            <a href="exportar_asistencia_excel.php?id_sesion=<?= $row['id_sesion'] ?>" 
                               class="btn btn-sm btn-outline-success">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </a>
                            <!-- Botón exportar a PDF -->
                            <a href="exportar_asistencia_pdf.php?id_sesion=<?= $row['id_sesion'] ?>" 
                               class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                        </td>
                    </tr>

                    <!-- Fila colapsable con la lista de asistentes -->
                    <tr class="collapse-row">
                        <td colspan="5" class="p-0">
                            <div class="collapse" id="asistentes<?= $row['id_sesion'] ?>">
                                <div class="p-3">
                                    <?php
                                    // Consultar asistentes de la sesión actual
                                    $asistentes = $conexion->query("
                                        SELECT ap.nombre, ap.apellido, ap.documento, a.hora_asistencia
                                        FROM asistencias a
                                        INNER JOIN aprendices ap ON a.id_aprendiz = ap.id_aprendiz
                                        WHERE a.id_sesion = {$row['id_sesion']}
                                        ORDER BY a.hora_asistencia ASC
                                    ");
                                    // Mostrar asistentes si existen
                                    if ($asistentes->num_rows > 0): ?>
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead>
                                                <tr class="table-light">
                                                    <th>Nombre</th>
                                                    <th>Apellido</th>
                                                    <th>Documento</th>
                                                    <th>Hora Asistencia</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($as = $asistentes->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($as['nombre']) ?></td>
                                                        <td><?= htmlspecialchars($as['apellido']) ?></td>
                                                        <td><?= htmlspecialchars($as['documento']) ?></td>
                                                        <td><?= htmlspecialchars($as['hora_asistencia']) ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <!-- Mensaje si no hay asistentes -->
                                        <p class="text-muted mb-0">No hay asistentes registrados en esta sesión.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <!-- Mensaje si no hay registros -->
                <?php if ($resultado->num_rows == 0): ?>
                    <tr><td colspan="5" class="text-center text-muted">No hay registros</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
