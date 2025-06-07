<?php 
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 3) {
    header('Location: login.php');
    exit;
}

$nombre_lector = $_SESSION['nombre'];
$id_lector = $_SESSION['usuario_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lector</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1>Bienvenido, <?= htmlspecialchars($nombre_lector) ?> (Lector)</h1>
    </header>

    <nav>
        <ul>
            <li><a href="libros_lect.php">Libros</a></li>
            <li><a href="Pages/revistas.html">Revistas</a></li>
            <li><a href="Pages/digital.html">Digital</a></li>
            <li><a href="vista_multas.php">Multas</a></li>
            <li><a href="vista_prest.php">Préstamos</a></li>
            <li><a href="Funciones/detalle_usuario.php?id=<?= $id_lector ?>">Ver info</a></li>
            <li>F.A.Q.</li>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Próximos Eventos</h2>
            <p>Espacio para imágenes y descripción de eventos culturales y actividades de la biblioteca.</p>
        </section>
        <section>
            <h2>Novedades</h2>
            <p>Espacio para mostrar las últimas adquisiciones o libros destacados.</p>
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
