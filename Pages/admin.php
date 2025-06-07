<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 1) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1>Panel del Administrador</h1>
    </header>

    <nav>
        <ul>
            <li><a href="usuarios.php">Gestionar Usuarios</a></li>
            <li><a href="registro_formulario_php.html">Registrar usuario</a></li>
            <li><a href="libros.php">Libros</a></li>
            <li><a href="revistas.php">Revistas</a></li>
            <li><a href="digital.php">Digitales</a></li>
            <li><a href="registrar_libro.html">Registrar Libros</a></li>
            <li><a href="registrar_revista.html">Registrar revista</a></li>
            <li><a href="registrar_digital.html">Registrar dgital</a></li>
            <li><a href="prestamos_gestion.php">Préstamos</a></li>
            <li><a href="gestion_multas.php">Multas</a></li>
            <li><a href="Pages/reportes.html">Reportes Mensuales</a></li>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Control del Sistema</h2>
            <p>Accede a reportes automáticos, gestiona usuarios, libros y sanciones conforme a las reglas establecidas.</p>
        </section>
        <section>
            <h2>Últimas Acciones</h2>
            <p>Visualiza las acciones recientes realizadas por los usuarios del sistema.</p>
        </section>
    </main>

    <aside>
        <h3>Soporte Técnico</h3>
        <p>Correo: soporte@biblioteca.edu</p>
        <p>Extensión: 101</p>
    </aside>
</body>
</html>
