<?php
// -------------------------------------------------------------
// Exportar Asistencias a Excel - SISVAEN
// -------------------------------------------------------------
// Este archivo genera un archivo Excel con los datos de asistencia
// de una sesión específica, utilizando PhpSpreadsheet.
//
// Flujo de trabajo:
// 1. Conexión a la base de datos y carga de dependencias.
// 2. Se consulta la información de la sesión y sus asistentes.
// 3. Se crea un archivo Excel con los datos obtenidos.
// 4. Se fuerza la descarga del archivo .xlsx.
// -------------------------------------------------------------

// ---------------- DEPENDENCIAS ----------------
require 'vendor/autoload.php';   // Autoload de Composer (PhpSpreadsheet)
require 'includes/conexion.php'; // Conexión a la base de datos

// Importamos las clases que usaremos de PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ---------------- VALIDAR Y OBTENER ID DE SESIÓN ----------------
$id_sesion = $_GET['id_sesion'] ?? 0;

// ---------------- CONSULTAR INFORMACIÓN DE LA SESIÓN ----------------
$sql = "
    SELECT s.descripcion, s.fecha_sesion, u.nombre AS enfermero
    FROM sesiones s
    INNER JOIN usuarios u ON s.id_usuario = u.id_usuario
    WHERE s.id_sesion = $id_sesion
";
$sesion = $conexion->query($sql)->fetch_assoc();

// ---------------- CONSULTAR ASISTENTES ----------------
$asistentes = $conexion->query("
    SELECT ap.nombre, ap.apellido, ap.documento, a.hora_asistencia
    FROM asistencias a
    INNER JOIN aprendices ap ON a.id_aprendiz = ap.id_aprendiz
    WHERE a.id_sesion = $id_sesion
    ORDER BY a.hora_asistencia ASC
");

// ---------------- CREAR DOCUMENTO EXCEL ----------------
$spreadsheet = new Spreadsheet();              // Creamos un nuevo documento Excel
$sheet = $spreadsheet->getActiveSheet();       // Obtenemos la hoja activa

// ---------------- INFORMACIÓN DE LA SESIÓN ----------------
$sheet->setCellValue('A1', 'Sesión: ' . $sesion['descripcion']);
$sheet->setCellValue('A2', 'Fecha: ' . $sesion['fecha_sesion']);
$sheet->setCellValue('A3', 'Enfermero: ' . $sesion['enfermero']);

// ---------------- ENCABEZADOS DE LA TABLA ----------------
$sheet->setCellValue('A5', 'Nombre');
$sheet->setCellValue('B5', 'Apellido');
$sheet->setCellValue('C5', 'Documento');
$sheet->setCellValue('D5', 'Hora Asistencia');

// ---------------- FILAS DE ASISTENTES ----------------
$row = 6; // Iniciamos desde la fila 6
while ($as = $asistentes->fetch_assoc()) {
    $sheet->setCellValue('A'.$row, $as['nombre']);
    $sheet->setCellValue('B'.$row, $as['apellido']);
    $sheet->setCellValue('C'.$row, $as['documento']);
    $sheet->setCellValue('D'.$row, $as['hora_asistencia']);
    $row++;
}

// ---------------- DESCARGA DEL ARCHIVO ----------------
$writer = new Xlsx($spreadsheet); // Definimos el escritor en formato XLSX
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="asistencia_sesion_'.$id_sesion.'.xlsx"');
header('Cache-Control: max-age=0');

// Guardamos el archivo en la salida (forzando descarga)
$writer->save('php://output');
exit;
