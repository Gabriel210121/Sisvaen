<?php
/*
 * Archivo: conexion.php
 * Autor: Gabriel Eduardo Trujillo Gonzalez - Desarrollador 
 * Fecha: 08/09/2025
 * Descripción: Establece la conexión a la base de datos MySQL
 *              del sistema SISVAEN utilizando la extensión mysqli.
 *              Configura el charset UTF-8 y maneja errores con 
 *              excepciones para mayor seguridad.
 */

$host = getenv("MYSQLHOST");       // Host de la DB (ej: mysql.railway.internal)
$user = getenv("MYSQLUSER");       // Usuario
$pass = getenv("MYSQLPASSWORD");   // Contraseña
$db   = getenv("MYSQLDATABASE");   // Nombre de la base de datos
$port = getenv("MYSQLPORT");       // Puerto (normalmente 3306)

// Reportar errores como excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Crear conexión
    $conexion = new mysqli($host, $user, $pass, $db, $port);

    // Configurar charset
    $conexion->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>