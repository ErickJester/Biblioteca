<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 3) {
    header('Location: ../login.php');
    exit;
}

include('../includes/conexion.php');
$nombreLector = $_SESSION['nombre'] ?? 'Lector';

// Consulta para obtener todos los libros
$sql = "SELECT 
            M.id_material, 
            M.titulo, 
            M.autor, 
            M.fecha_registro, 
            M.cantidad, 
            M.genero, 
            L.edicion, 
            L.editorial, 
            L.calificacion
        FROM Material M
        JOIN Libro L ON M.id_material = L.id_libro";

$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros</title>
</head>
<body>
    <header>
        <h1>Libros disponibles</h1>
        <div>
            <span>Sesión activa como <?= htmlspecialchars($nombreLector) ?> (Lector)</span>
        </div>
    </header>

    <nav>
        <ul>
            <li>Buscar</li>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Lista de Libros</h2>
            <?php if ($resultado->num_rows > 0): ?>
                <table border="1" cellpadding="5">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Fecha</th>
                            <th>Cantidad</th>
                            <th>Género</th>
                            <th>Editorial</th>
                            <th>Edición</th>
                            <th>Calificación</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['titulo']) ?></td>
                                <td><?= htmlspecialchars($fila['autor']) ?></td>
                                <td><?= htmlspecialchars($fila['fecha_registro']) ?></td>
                                <td><?= htmlspecialchars($fila['cantidad']) ?></td>
                                <td><?= htmlspecialchars($fila['genero']) ?></td>
                                <td><?= htmlspecialchars($fila['editorial']) ?></td>
                                <td><?= htmlspecialchars($fila['edicion']) ?></td>
                                <td><?= htmlspecialchars($fila['calificacion']) ?></td>
                                <td>
                                    <a href="pedido_prest.php?id=<?= $fila['id_material'] ?>">Pedir préstamo</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay libros registrados.</p>
            <?php endif; ?>
        </section>
    </main>

    <aside>
        <h3>Contacto</h3>
        <p>Teléfono: 555-555-555</p>
        <p>Dirección: Calle Principal 123, Ciudad</p>
        <div style="margin-top: 1rem;">
            <span style="margin: 0 0.5rem;">[FB]</span>
            <span style="margin: 0 0.5rem;">[TW]</span>
            <span style="margin: 0 0.5rem;">[IG]</span>
        </div>
        <div style="margin-top: 1rem; height: 200px; background: #2c3e50; display: flex; align-items: center; justify-content: center;">
            [Mapa de ubicación]
        </div>
    </aside>
</body>
</html>
