<?php
$host = "localhost";        // o IP del servidor de base de datos
$usuario = "root";          // cambia esto si tienes otro usuario
$contrasena = "";           // cambia esto si tu usuario tiene contraseña
$base_datos = "base_biblioteca"; // nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Opcional: establecer codificación a UTF-8
$conn->set_charset("utf8");
?>
