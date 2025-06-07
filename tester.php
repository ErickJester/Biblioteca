<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once __DIR__ . '/includes/conexion.php';
echo $conn->host_info
    ? "Conexión OK a {$base_datos}."
    : "Error.";
$conn->close();
