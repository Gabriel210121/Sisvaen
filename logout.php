<?php
/*
 * Archivo: logout.php
 * Autor: Gabriel
 * Fecha: 08/09/2025
 * Descripción: Cierra la sesión activa del usuario en el 
 *              sistema SISVAEN. Destruye las variables de 
 *              sesión y redirige al formulario de inicio 
 *              de sesión (login.php).
 */

session_start();
session_destroy();
header("Location: login.php");
exit;
?>
