<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/conexion.php';
session_start();
include '../includes/multas_auto.php';

function obtenerIntentos($code_user, $conn) {
    $stmt = $conn->prepare("SELECT intentos, ultimo_intento FROM IntentoLogin WHERE code_user = ?");
    $stmt->bind_param("s", $code_user);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $res;
}

function actualizarIntento($code_user, $conn) {
    $datos = obtenerIntentos($code_user, $conn);
    if ($datos) {
        $intentos = $datos['intentos'] + 1;
        $stmt = $conn->prepare("UPDATE IntentoLogin SET intentos = ?, ultimo_intento = NOW() WHERE code_user = ?");
        $stmt->bind_param("is", $intentos, $code_user);
    } else {
        $intentos = 1;
        $stmt = $conn->prepare("INSERT INTO IntentoLogin (code_user, intentos, ultimo_intento) VALUES (?, ?, NOW())");
        $stmt->bind_param("si", $code_user, $intentos);
    }
    $stmt->execute();
    $stmt->close();
}

function limpiarIntentos($code_user, $conn) {
    $stmt = $conn->prepare("DELETE FROM IntentoLogin WHERE code_user = ?");
    $stmt->bind_param("s", $code_user);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['user']);
    $contrasena = trim($_POST['password']);

    $bloqueo = obtenerIntentos($usuario, $conn);
    if ($bloqueo && $bloqueo['intentos'] >= 3) {
        $ahora = time();
        $ultimo = strtotime($bloqueo['ultimo_intento']);
        if (($ahora - $ultimo) < 30) {
            echo "<script>alert('Cuenta bloqueada temporalmente. Intenta de nuevo en 30 segundos.');</script>";
            exit;
        } else {
            limpiarIntentos($usuario, $conn); // Se levanta el bloqueo pasado el tiempo
        }
    }

    $stmt = $conn->prepare("SELECT id_usuario, tipo_usuario, contrasena FROM Usuario WHERE code_user = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($fila = $result->fetch_assoc()) {
        if ($contrasena === $fila['contrasena']) {
            limpiarIntentos($usuario, $conn);
            $_SESSION['usuario_id'] = $fila['id_usuario'];
            $_SESSION['tipo_usuario'] = $fila['tipo_usuario'];
            $_SESSION['nombre'] = $usuario;

            switch ($fila['tipo_usuario']) {
                case 1: header("Location: admin.php"); exit;
                case 2: header("Location: biblio.php"); exit;
                case 3: header("Location: lector.php"); exit;
            }
        } else {
            actualizarIntento($usuario, $conn);
            echo "<script>alert('Contraseña incorrecta.');</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <header><h1>Iniciar sesión</h1></header>
    <form action="" method="post">
        <label for="user">Usuario:</label>
        <input type="text" id="user" name="user" placeholder="Ingresa tu usuario" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" placeholder="####" required><br>

        <button type="submit">Enviar</button>
    </form>
</body>
</html>
