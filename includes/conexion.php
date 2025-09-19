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
$host = "localhost";   // Dirección del servidor MySQL
$user = "root";        // Usuario de la base de datos
$pass = "";            // Contraseña del usuario
$db   = "sisvaen";     // Nombre de la base de datos (cámbialo si es necesario)

//REPORTAR ERRORES DE MYSQLI 
// Configuramos mysqli para que lance excepciones en lugar de warnings.
// Esto facilita la captura de errores con try/catch.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // CREAR CONEXIÓN 
    $conexion = new mysqli($host, $user, $pass, $db);

    // CONFIGURAR CHARSET 
    $conexion->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // MANEJO DE ERRORES 
    die("❌ Error de conexión: " . $e->getMessage());
}
?>
