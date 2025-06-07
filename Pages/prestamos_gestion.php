<?php
session_start();
include('../includes/conexion.php');
include('../includes/multas_auto.php');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], [1, 2])) {
    header('Location: login.php');
    exit;
}

// Actualizar automáticamente estados vencidos
$conn->query("UPDATE Prestamo 
              SET estado = 'vencido' 
              WHERE estado = 'activo' 
                AND CURDATE() > fecha_vencimiento");

// Filtros de búsqueda
$busqueda = $_GET['buscar'] ?? '';
$estado   = $_GET['estado'] ?? '';
$desde    = $_GET['desde'] ?? '';
$hasta    = $_GET['hasta'] ?? '';

// Consulta base
$sql = "SELECT P.id_prestamo, U.nombre AS usuario, M.titulo AS material, 
       P.fecha_prestamo, P.fecha_vencimiento, P.fecha_devolucion,
       CASE 
           WHEN P.estado = 'activo' AND CURDATE() > P.fecha_vencimiento THEN 'vencido'
           ELSE P.estado
       END AS estado
        FROM Prestamo P
        JOIN Usuario U ON P.id_usuario = U.id_usuario
        JOIN Material M ON P.id_material = M.id_material
        WHERE 1=1";

// Agrega condiciones si se usan filtros
if (!empty($busqueda)) {
    $busqueda = $conn->real_escape_string($busqueda);
    $sql .= " AND (
        U.nombre LIKE '%$busqueda%' OR
        M.titulo LIKE '%$busqueda%'
    )";
}

if (!empty($estado)) {
    $estado = $conn->real_escape_string($estado);
    $sql .= " AND P.estado = '$estado'";
}

if (!empty($desde)) {
    $sql .= " AND P.fecha_prestamo >= '$desde'";
}

if (!empty($hasta)) {
    $sql .= " AND P.fecha_prestamo <= '$hasta'";
}

$sql .= " ORDER BY P.fecha_prestamo DESC";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Préstamos</title>
    <link rel="stylesheet" href="../Styles/prest_style.css">
</head>

<body>

    <h1>Gestión de Préstamos</h1>

    <form method="get">
        <input type="text" name="buscar" placeholder="Usuario o material" value="<?= htmlspecialchars($busqueda) ?>">
        <select name="estado">
            <option value="">-- Estado --</option>
            <option value="activo" <?= $estado == 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="vencido" <?= $estado == 'vencido' ? 'selected' : '' ?>>Vencido</option>
            <option value="pagado" <?= $estado == 'pagado' ? 'selected' : '' ?>>Pagado</option>
        </select>
        Desde: <input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>">
        Hasta: <input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
        <button type="submit">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Material</th>
                <th>Fecha Préstamo</th>
                <th>Vencimiento</th>
                <th>Fecha Devolución</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_prestamo'] ?></td>
                        <td><?= $row['usuario'] ?></td>
                        <td><?= $row['material'] ?></td>
                        <td><?= $row['fecha_prestamo'] ?></td>
                        <td><?= $row['fecha_vencimiento'] ?? 'N/A' ?></td>
                        <td><?= $row['fecha_devolucion'] ?? 'Pendiente' ?></td>
                        <td><?= ucfirst($row['estado']) ?></td>
                        <td><a href="edit_prest.php?id=<?= $row['id_prestamo'] ?>">Ver</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No se encontraron resultados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
