<?php
include 'conexion.php';

$hoy = date('Y-m-d');

// 1. Buscar préstamos vencidos y no devueltos
$sql = "SELECT P.id_prestamo, P.id_usuario, P.fecha_vencimiento
        FROM Prestamo P
        WHERE P.estado = 'vencido' AND P.fecha_devolucion IS NULL";

$resultado = $conn->query($sql);

while ($fila = $resultado->fetch_assoc()) {
    $id_prestamo = $fila['id_prestamo'];
    $id_usuario = $fila['id_usuario'];
    $fecha_venc = new DateTime($fila['fecha_vencimiento']);
    $fecha_hoy = new DateTime($hoy);

    $dias_atraso = $fecha_venc->diff($fecha_hoy)->days;
    if ($dias_atraso < 0) continue;

    $multa_total = $dias_atraso * 200;

    // Revisar si ya existe una multa para este préstamo
    $stmt_check = $conn->prepare("SELECT id_multa FROM Multa WHERE id_prestamo = ? AND pagada = 0");
    $stmt_check->bind_param("i", $id_prestamo);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        // Actualizar multa existente
        $stmt_update = $conn->prepare("UPDATE Multa SET monto = ?, fecha_multa = ?, motivo = ? WHERE id_prestamo = ? AND pagada = 0");
        $motivo = "Retraso en devolución, $dias_atraso día(s)";
        $stmt_update->bind_param("issi", $multa_total, $hoy, $motivo, $id_prestamo);
        $stmt_update->execute();
    } else {
        // Insertar nueva multa
        $stmt_insert = $conn->prepare("INSERT INTO Multa (id_usuario, id_prestamo, monto, motivo, fecha_multa) VALUES (?, ?, ?, ?, ?)");
        $motivo = "Retraso en devolución, $dias_atraso día(s)";
        $stmt_insert->bind_param("iiiss", $id_usuario, $id_prestamo, $multa_total, $motivo, $hoy);
        $stmt_insert->execute();
    }
}
?>
