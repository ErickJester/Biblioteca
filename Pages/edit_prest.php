<?php
session_start();
include('../includes/conexion.php');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], [1, 2])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>ID de préstamo no válido.</p>";
    exit;
}

// Obtener datos actuales del préstamo
$stmt = $conn->prepare("SELECT P.id_prestamo, P.id_usuario, U.nombre AS usuario, M.titulo AS material, P.estado 
                        FROM Prestamo P 
                        JOIN Usuario U ON P.id_usuario = U.id_usuario 
                        JOIN Material M ON P.id_material = M.id_material 
                        WHERE P.id_prestamo = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo "<p>Préstamo no encontrado.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_estado = $_POST['estado'];
    $hoy = date('Y-m-d');
    $id_usuario = $row['id_usuario'];

    if ($nuevo_estado === 'vencido') {
        // Solo actualizar estado y fecha de vencimiento. La multa será actualizada por multas_auto.php
        $upd = $conn->prepare("UPDATE Prestamo SET estado = ?, fecha_vencimiento = ? WHERE id_prestamo = ?");
        $upd->bind_param("ssi", $nuevo_estado, $hoy, $id);
        $upd->execute();
    } elseif ($nuevo_estado === 'pagado') {
        $upd = $conn->prepare("UPDATE Prestamo SET estado = ?, fecha_devolucion = ? WHERE id_prestamo = ?");
        $upd->bind_param("ssi", $nuevo_estado, $hoy, $id);
        $upd->execute();
    } else {
        $upd = $conn->prepare("UPDATE Prestamo SET estado = ? WHERE id_prestamo = ?");
        $upd->bind_param("si", $nuevo_estado, $id);
        $upd->execute();
    }

    header("Location: prestamos_gestion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Préstamo</title>
</head>
<body>
    <h1>Editar estado del préstamo</h1>
    <p><strong>Usuario:</strong> <?= htmlspecialchars($row['usuario']) ?></p>
    <p><strong>Material:</strong> <?= htmlspecialchars($row['material']) ?></p>

    <form method="post">
        <label for="estado">Estado:</label>
        <select name="estado" id="estado">
            <option value="activo" <?= $row['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="vencido" <?= $row['estado'] == 'vencido' ? 'selected' : '' ?>>Vencido</option>
            <option value="pagado" <?= $row['estado'] == 'pagado' ? 'selected' : '' ?>>Pagado</option>
        </select>
        <button type="submit">Guardar cambios</button>
    </form>

    <p><a href="prestamos_gestion.php">← Volver</a></p>
</body>
</html>
