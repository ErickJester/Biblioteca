<?php
include('../../includes/conexion.php');

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("ID de usuario no válido.");
}

session_start();

$id = $_GET['id'] ?? null;

// Si es lector, solo puede ver su propio ID
if ($_SESSION['tipo_usuario'] === 3 && $_SESSION['usuario_id'] != $id) {
    die("No tienes permiso para ver otros usuarios.");
}


// Consulta datos base del usuario
$stmt = $conn->prepare("SELECT * FROM Usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Usuario no encontrado.");
}

$usuario = $result->fetch_assoc();

// Detectar rol para obtener datos extendidos
$rol = (int)$usuario['tipo_usuario'];
$extra = [];

if ($rol === 2) {
    $res = $conn->query("SELECT telefono FROM Bibliotecario WHERE id_biblio = $id");
    $extra = $res->fetch_assoc() ?? [];
} elseif ($rol === 3) {
    $res = $conn->query("SELECT telefono, correo FROM Lector WHERE id_lector = $id");
    $extra = $res->fetch_assoc() ?? [];
}

// Función auxiliar
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
    <title>Detalle de Usuario</title>
    <link rel="stylesheet" href="usuarios.css">
</head>
<body>
    <h1>Detalle de Usuario</h1>

    <table>
        <tr><th>ID</th><td><?= $usuario['id_usuario'] ?></td></tr>
        <tr><th>Nombre</th><td><?= htmlspecialchars($usuario['nombre']) ?></td></tr>
        <tr><th>Apellido Paterno</th><td><?= htmlspecialchars($usuario['apellidoP']) ?></td></tr>
        <tr><th>Apellido Materno</th><td><?= htmlspecialchars($usuario['apellidoM']) ?></td></tr>
        <tr><th>Código Usuario</th><td><?= htmlspecialchars($usuario['code_user']) ?></td></tr>
        <tr><th>Tipo</th><td><?= tipoTexto($usuario['tipo_usuario']) ?></td></tr>
        <tr><th>Fecha de Nacimiento</th><td><?= $usuario['fecha_nacimiento'] ?></td></tr>
        <tr><th>Fecha de Registro</th><td><?= $usuario['fecha_registro'] ?></td></tr>
        <?php if (!empty($extra['telefono'])): ?>
        <tr><th>Teléfono</th><td><?= htmlspecialchars($extra['telefono']) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($extra['correo'])): ?>
        <tr><th>Correo</th><td><?= htmlspecialchars($extra['correo']) ?></td></tr>
        <?php endif; ?>
    </table>

    <p style="text-align: center; margin-top: 20px;">
        <a href="javascript:history.back()">← Volver</a>
    </p>
</body>
</html>
