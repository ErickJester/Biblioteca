<?php
session_start();
include('../includes/conexion.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 3) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];

$sql = "SELECT P.id_prestamo, M.titulo AS material, P.fecha_prestamo, P.fecha_vencimiento,
               P.fecha_devolucion, P.estado
        FROM Prestamo P
        JOIN Material M ON P.id_material = M.id_material
        WHERE P.id_usuario = ?
        ORDER BY P.fecha_prestamo DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Préstamos</title>
    <link rel="stylesheet" href="../Styles/prest_style.css">
</head>
<body>
    <h1>Mis Préstamos</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Material</th>
                    <th>Fecha Préstamo</th>
                    <th>Vencimiento</th>
                    <th>Fecha Devolución</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_prestamo'] ?></td>
                        <td><?= htmlspecialchars($row['material']) ?></td>
                        <td><?= $row['fecha_prestamo'] ?></td>
                        <td><?= $row['fecha_vencimiento'] ?></td>
                        <td><?= $row['fecha_devolucion'] ?? 'Pendiente' ?></td>
                        <td><?= ucfirst($row['estado']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tienes préstamos registrados.</p>
    <?php endif; ?>

    <p><a href="lector.php">← Volver al inicio</a></p>
</body>
</html>
