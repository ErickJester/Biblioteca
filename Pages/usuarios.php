<?php
include('../includes/conexion.php');

// Procesar búsqueda y filtrado
$busqueda = $_GET['buscar'] ?? '';
$filtro = $_GET['filtro'] ?? '';

// Consulta base
$sql = "SELECT id_usuario, nombre, apellidoP, apellidoM, code_user, tipo_usuario
        FROM Usuario 
        WHERE 1=1";

// Agrega filtros si se aplican
if (!empty($busqueda)) {
    $busqueda = $conn->real_escape_string($busqueda);
    $sql .= " AND (
        nombre LIKE '%$busqueda%' OR 
        apellidoP LIKE '%$busqueda%' OR 
        apellidoM LIKE '%$busqueda%' OR 
        code_user LIKE '%$busqueda%'
    )";
}

if (!empty($filtro)) {
    $tipo = match ($filtro) {
        'Administrador' => 1,
        'Bibliotecario' => 2,
        'Lector'        => 3,
        default         => null
    };
    if ($tipo !== null) {
        $sql .= " AND tipo_usuario = $tipo";
    }
}

$sql .= " ORDER BY nombre ASC";
$resultado = $conn->query($sql);

// Función corregida
function tipoTexto($tipo) {
    return match ((int)$tipo) {
        1 => 'Administrador',
        2 => 'Bibliotecario',
        3 => 'Lector',
        default => 'Desconocido'
    };
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Registrados</title>
    <link rel="stylesheet" href="../Styles/usuarios.css">
</head>
<body>

<h1>Listado de Usuarios</h1>

<form method="get">
    <input type="text" name="buscar" placeholder="Buscar nombre o código..." value="<?= htmlspecialchars($busqueda) ?>">
    <select name="filtro">
        <option value="">-- Tipo de Usuario --</option>
        <option value="Administrador" <?= $filtro == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
        <option value="Bibliotecario" <?= $filtro == 'Bibliotecario' ? 'selected' : '' ?>>Bibliotecario</option>
        <option value="Lector" <?= $filtro == 'Lector' ? 'selected' : '' ?>>Lector</option>
    </select>
    <input type="submit" value="Filtrar">
</form>

<?php if ($resultado && $resultado->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre Completo</th>
            <th>Código Usuario</th>
            <th>Tipo de Usuario</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_usuario'] ?></td>
            <td><?= htmlspecialchars($row['nombre'] . " " . $row['apellidoP'] . " " . $row['apellidoM']) ?></td>
            <td><?= htmlspecialchars($row['code_user']) ?></td>
            <td><?= tipoTexto($row['tipo_usuario']) ?></td>
            <td><a href="Funciones/detalle_usuario.php?id=<?= $row['id_usuario'] ?>">Ver detalles</a></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
    <p style="text-align:center;">No se encontraron usuarios con esos criterios.</p>
<?php endif; ?>

</body>
</html>
