<?php
session_start();
include('../includes/conexion.php');

// Validación de sesión y tipo de usuario
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 3) {
    header('Location: ../login.php');
    exit;
}

// Validar que se recibió un id_material por GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>ID de material inválido.</p>";
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_material = intval($_GET['id']);
$fecha_prestamo = date('Y-m-d');
$fecha_vencimiento = date('Y-m-d', strtotime('+3 days'));
$fecha_devolucion = date('Y-m-d', strtotime('+7 days')); // préstamo por 7 días
$estado = 'Activo';

// Validar disponibilidad (opcional)
$verifica = $conn->prepare("SELECT cantidad FROM Material WHERE id_material = ?");
$verifica->bind_param("i", $id_material);
$verifica->execute();
$res = $verifica->get_result();
if ($row = $res->fetch_assoc()) {
    if ((int)$row['cantidad'] <= 0) {
        echo "<p>El material no está disponible para préstamo.</p>";
        exit;
    }
} else {
    echo "<p>Material no encontrado.</p>";
    exit;
}
$verifica->close();

// Insertar el préstamo
$sql = "INSERT INTO Prestamo (id_usuario, id_material, fecha_prestamo, fecha_vencimiento, estado) 
        VALUES (?, ?, ?, ?, 'activo')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $id_usuario, $id_material, $fecha_prestamo, $fecha_vencimiento);


if ($stmt->execute()) {
    echo "<p>Préstamo registrado correctamente.</p>";
    echo "<a href='libros_lect.php'>Volver a libros</a>";
} else {
    echo "<p>Error al registrar el préstamo.</p>";
}

$stmt->close();
$conn->close();
?>
