<?php
session_start();
include('../includes/conexion.php');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], [1, 2])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<p>ID de multa no válido.</p>";
    exit;
}

// Obtener datos de la multa
$stmt = $conn->prepare("SELECT M.id_multa, M.id_prestamo, M.id_usuario, U.nombre AS usuario, M.monto, M.motivo, M.fecha_multa, M.pagada
                        FROM Multa M
                        JOIN Usuario U ON M.id_usuario = U.id_usuario
                        WHERE M.id_multa = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$multa = $result->fetch_assoc()) {
    echo "<p>Multa no encontrada.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pagada = 1;
    $fecha_pago = date('Y-m-d');

    // 1. Marcar multa como pagada
    $upd = $conn->prepare("UPDATE Multa SET pagada = ? WHERE id_multa = ?");
    $upd->bind_param("ii", $pagada, $id);
    $upd->execute();

    // 2. Cambiar estado del préstamo si existe
    if (!empty($multa['id_prestamo'])) {
        $stmt2 = $conn->prepare("UPDATE Prestamo SET estado = 'pagado', fecha_devolucion = ? WHERE id_prestamo = ?");
        $stmt2->bind_param("si", $fecha_pago, $multa['id_prestamo']);
        $stmt2->execute();
    }

    header("Location: gestion_multas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Multa</title>
</head>
<body>
    <h1>Editar Multa</h1>

    <p><strong>Usuario:</strong> <?= htmlspecialchars($multa['usuario']) ?></p>
    <p><strong>Monto:</strong> $<?= number_format($multa['monto'], 2) ?></p>
    <p><strong>Motivo:</strong> <?= htmlspecialchars($multa['motivo']) ?></p>
    <p><strong>Fecha de multa:</strong> <?= $multa['fecha_multa'] ?></p>
    <p><strong>Estado:</strong> <?= $multa['pagada'] ? 'Pagada' : 'No pagada' ?></p>

    <?php if (!$multa['pagada']): ?>
        <form method="post">
            <button type="submit">Marcar como pagada</button>
        </form>
    <?php else: ?>
        <p>Esta multa ya ha sido pagada.</p>
    <?php endif; ?>

    <p><a href="gestion_multas.php">← Volver</a></p>
</body>
</html>
