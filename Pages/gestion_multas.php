<?php
session_start();
include('../includes/conexion.php');

include('../includes/multas_auto.php');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], [1, 2])) {
    header('Location: login.php');
    exit;
}

// Filtros
$busqueda = $_GET['buscar'] ?? '';
$pagada   = $_GET['pagada'] ?? '';
$desde    = $_GET['desde'] ?? '';
$hasta    = $_GET['hasta'] ?? '';

// Consulta base
$sql = "SELECT M.id_multa, U.nombre AS usuario, M.monto, M.motivo, M.fecha_multa, M.pagada
        FROM Multa M
        JOIN Usuario U ON M.id_usuario = U.id_usuario
        WHERE 1=1";

// Filtro de búsqueda general
if (!empty($busqueda)) {
    $busqueda = $conn->real_escape_string($busqueda);
    $sql .= " AND (
        U.nombre LIKE '%$busqueda%' OR
        M.motivo LIKE '%$busqueda%'
    )";
}

// Filtro por estado de pago
if ($pagada !== '') {
    $estado_pagada = $pagada === '1' ? 1 : 0;
    $sql .= " AND M.pagada = $estado_pagada";
}

// Filtro por fecha
if (!empty($desde)) {
    $sql .= " AND M.fecha_multa >= '$desde'";
}
if (!empty($hasta)) {
    $sql .= " AND M.fecha_multa <= '$hasta'";
}

$sql .= " ORDER BY M.fecha_multa DESC";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Multas</title>
    <link rel="stylesheet" href="../Styles/prest_style.css">
</head>
<body>
    <h1>Gestión de Multas</h1>

    <form method="get">
        <input type="text" name="buscar" placeholder="Usuario o motivo" value="<?= htmlspecialchars($busqueda) ?>">
        <select name="pagada">
            <option value="">-- Estado --</option>
            <option value="1" <?= $pagada === '1' ? 'selected' : '' ?>>Pagada</option>
            <option value="0" <?= $pagada === '0' ? 'selected' : '' ?>>No pagada</option>
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
                <th>Monto</th>
                <th>Motivo</th>
                <th>Fecha</th>
                <th>Pagada</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_multa'] ?></td>
                        <td><?= htmlspecialchars($row['usuario']) ?></td>
                        <td>$<?= number_format($row['monto'], 2) ?></td>
                        <td><?= htmlspecialchars($row['motivo']) ?></td>
                        <td><?= $row['fecha_multa'] ?></td>
                        <td><?= $row['pagada'] ? 'Sí' : 'No' ?></td>
                        <td><a href="edit_multas.php?id=<?= $row['id_multa'] ?>">Editar</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No se encontraron resultados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
