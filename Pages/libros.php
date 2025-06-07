<?php
session_start();
$sesionActiva = isset($_SESSION['usuario_id']);
include('../includes/conexion.php');

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
        <h1>Libros</h1>
        <div>
            <?php if (!$sesionActiva): ?>
                <a href="login.html">Iniciar sesión</a>
                <a href="registro.html">Registrarse</a>
            <?php else: ?>
                <span>Sesión activa como <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></span>
            <?php endif; ?>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($fila['autor']); ?></td>
                                <td><?php echo htmlspecialchars($fila['fecha_registro']); ?></td>
                                <td><?php echo htmlspecialchars($fila['cantidad']); ?></td>
                                <td><?php echo htmlspecialchars($fila['genero']); ?></td>
                                <td><?php echo htmlspecialchars($fila['editorial']); ?></td>
                                <td><?php echo htmlspecialchars($fila['edicion']); ?></td>
                                <td><?php echo htmlspecialchars($fila['calificacion']); ?></td>
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
