<?php
// Cargamos las librerías necesarias
require 'vendor/autoload.php';   // Autoload de Composer (para usar FPDF u otras librerías)
require 'includes/conexion.php'; // Conexión a la base de datos

// ---------------- VALIDAR ID DE SESIÓN ----------------
// Obtenemos el parámetro "id_sesion" de la URL y lo convertimos a entero
$id_sesion = isset($_GET['id_sesion']) ? intval($_GET['id_sesion']) : 0;

// Si el id no es válido (0 o negativo) se detiene el script
if ($id_sesion <= 0) {
    die("ID de sesión inválido.");
}

// ---------------- CONSULTA DE INFORMACIÓN DE LA SESIÓN ----------------
// Preparamos la consulta para obtener la información de la sesión
$stmt = $conexion->prepare("
    SELECT s.descripcion, s.fecha_sesion, u.nombre AS enfermero
    FROM sesiones s
    INNER JOIN usuarios u ON s.id_usuario = u.id_usuario
    WHERE s.id_sesion = ?
");

// Asignamos el parámetro id_sesion a la consulta
$stmt->bind_param("i", $id_sesion);
$stmt->execute();

// Guardamos el resultado de la consulta en un arreglo asociativo
$sesion = $stmt->get_result()->fetch_assoc();

// Si no se encontró la sesión, detenemos el script
if (!$sesion) {
    die("No se encontró la sesión.");
}

// ---------------- CONSULTA DE LOS ASISTENTES ----------------
// Preparamos la consulta para obtener los aprendices que asistieron a esa sesión
$stmt = $conexion->prepare("
    SELECT ap.nombre, ap.apellido, ap.documento, a.hora_asistencia
    FROM asistencias a
    INNER JOIN aprendices ap ON a.id_aprendiz = ap.id_aprendiz
    WHERE a.id_sesion = ?
    ORDER BY a.hora_asistencia ASC
");

// Asignamos el id_sesion a la consulta
$stmt->bind_param("i", $id_sesion);
$stmt->execute();

// Guardamos la lista de asistentes
$asistentes = $stmt->get_result();

// ---------------- GENERACIÓN DEL PDF ----------------
// Creamos un nuevo documento PDF
$pdf = new \FPDF();
$pdf->AddPage(); // Añadimos una página en blanco

// ---------------- TÍTULO ----------------
$pdf->SetFont('Arial','B',16); // Fuente Arial, negrita, tamaño 16
$pdf->Cell(0,10, utf8_decode('Reporte de Asistencia'),0,1,'C'); // Título centrado
$pdf->Ln(5); // Salto de línea

// ---------------- INFORMACIÓN DE LA SESIÓN ----------------
$pdf->SetFont('Arial','',12); // Fuente normal, tamaño 12
$pdf->Cell(0,8, utf8_decode('Sesión: '.$sesion['descripcion']),0,1);
$pdf->Cell(0,8, utf8_decode('Fecha: '.$sesion['fecha_sesion']),0,1);
$pdf->Cell(0,8, utf8_decode('Enfermero: '.$sesion['enfermero']),0,1);
$pdf->Ln(8); // Espacio antes de la tabla

// ---------------- ENCABEZADO DE LA TABLA ----------------
$pdf->SetFont('Arial','B',10); // Fuente negrita, tamaño 10
$pdf->Cell(45,8,'Nombre',1);           // Columna Nombre
$pdf->Cell(45,8,'Apellido',1);         // Columna Apellido
$pdf->Cell(40,8,'Documento',1);        // Columna Documento
$pdf->Cell(60,8,'Hora Asistencia',1);  // Columna Hora Asistencia
$pdf->Ln(); // Salto de línea después del encabezado

// ---------------- FILAS DE LA TABLA (ASISTENTES) ----------------
$pdf->SetFont('Arial','',10); // Fuente normal, tamaño 10
while ($as = $asistentes->fetch_assoc()) {
    $pdf->Cell(45,8,utf8_decode($as['nombre']),1);
    $pdf->Cell(45,8,utf8_decode($as['apellido']),1);
    $pdf->Cell(40,8,$as['documento'],1);
    $pdf->Cell(60,8,$as['hora_asistencia'],1);
    $pdf->Ln(); // Salto de línea después de cada fila
}

// ---------------- DESCARGA DEL PDF ----------------
// Se genera el archivo PDF para descargarlo automáticamente
$pdf->Output('D', 'asistencia_sesion_'.$id_sesion.'.pdf');
exit; // Finaliza el script
?>
