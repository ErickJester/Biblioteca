<?php
session_start();
include('../includes/conexion.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 3) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

$sql = "SELECT M.id_multa, M.monto, M.motivo, M.fecha_multa, M.pagada
        FROM Multa M
        WHERE M.id_usuario = ?
        ORDER BY M.fecha_multa DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Multas</title>
    <link rel="stylesheet" href="../Styles/prest_style.css">
</head>
<body>
    <h1>Mis Multas</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Monto</th>
                    <th>Motivo</th>
                    <th>Fecha</th>
                    <th>Pagada</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_multa'] ?></td>
                        <td>$<?= number_format($row['monto'], 2) ?></td>
                        <td><?= htmlspecialchars($row['motivo']) ?></td>
                        <td><?= $row['fecha_multa'] ?></td>
                        <td><?= $row['pagada'] ? 'Sí' : 'No' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tienes multas registradas.</p>
    <?php endif; ?>

    <p><a href="lector.php">← Volver al inicio</a></p>
</body>
</html>
