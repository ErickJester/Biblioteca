<?php
// Habilitar visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
require_once __DIR__ . '/../includes/conexion.php';
session_start();

// Asegurar que la petición sea POST
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {
    http_response_code(405);
    die('Método no permitido.');
}

// Recoger y sanitizar datos del formulario
$nombre           = trim($_POST['nombre'] ?? '');
$apellidoP        = trim($_POST['apellidoP'] ?? '');
$apellidoM        = trim($_POST['apellidoM'] ?? '');
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$tipo_usuario     = $_POST['tipo_usuario'] ?? '';
$telefono         = trim($_POST['telefono'] ?? '');
$correo           = trim($_POST['correo'] ?? '');

// Validar campos requeridos
$errores = [];
if ($nombre === '')           $errores[] = 'El nombre es obligatorio.';
if ($apellidoP === '')        $errores[] = 'El apellido paterno es obligatorio.';
if ($apellidoM === '')        $errores[] = 'El apellido materno es obligatorio.';
if ($fecha_nacimiento === '') $errores[] = 'La fecha de nacimiento es obligatoria.';
if ($tipo_usuario === '')     $errores[] = 'El tipo de usuario es obligatorio.';

if (!empty($errores)) {
    // Mostrar errores y detener ejecución
    foreach ($errores as $e) {
        echo "- $e<br>";
    }
    exit;
}

// Mapear tipo de usuario a prefijo y longitud de código
switch ($tipo_usuario) {
    case 'Administrador':
        $prefijo    = 'A';
        $longitud   = 3;
        $tipo_valor = 1;
        break;
    case 'Bibliotecario':
        $prefijo    = 'B';
        $longitud   = 3;
        $tipo_valor = 2;
        break;
    case 'Lector':
        $prefijo    = 'C';
        $longitud   = 4;
        $tipo_valor = 3;
        break;
    default:
        die('Tipo de usuario no válido.');
}

// Obtener el último código existente para ese prefijo
$query = $conn->prepare(
    "SELECT code_user FROM Usuario WHERE code_user LIKE ? ORDER BY code_user DESC LIMIT 1"
);
$likePattern = $prefijo . '%';
$query->bind_param('s', $likePattern);
$query->execute();
$result = $query->get_result();

if ($row = $result->fetch_assoc()) {
    $ultimoNum = intval(substr($row['code_user'], 1));
    $nuevoNum  = $ultimoNum + 1;
} else {
    $nuevoNum = 1;
}

$code_user = $prefijo . str_pad($nuevoNum, $longitud, '0', STR_PAD_LEFT);

// Generar contraseña aleatoria (8 caracteres hexadecimales)
$contrasena = bin2hex(random_bytes(4));

// Iniciar transacción para consistencia
$conn->begin_transaction();

try {
    // 1) Insertar en la tabla Usuario
    $sqlUsuario = "INSERT INTO Usuario 
        (tipo_usuario, contrasena, code_user, nombre, apellidoP, apellidoM, fecha_nacimiento, fecha_registro)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sqlUsuario);
    $stmt->bind_param(
        'issssss',
        $tipo_valor,
        $contrasena,
        $code_user,
        $nombre,
        $apellidoP,
        $apellidoM,
        $fecha_nacimiento
    );

    if (!$stmt->execute()) {
        throw new Exception('Error al insertar en Usuario: ' . $stmt->error);
    }

    // Obtener el ID recién generado
    $id_usuario = $conn->insert_id;

    // 2) Insertar en la tabla especializada según tipo
    switch ($tipo_usuario) {
        case 'Administrador':
            $sqlEsp = "INSERT INTO Administrador (id_admin) VALUES (?)";
            $stmt   = $conn->prepare($sqlEsp);
            $stmt->bind_param('i', $id_usuario);
            break;

        case 'Bibliotecario':
            $sqlEsp = "INSERT INTO Bibliotecario (id_biblio, telefono) VALUES (?, ?)";
            $stmt   = $conn->prepare($sqlEsp);
            $stmt->bind_param('is', $id_usuario, $telefono);
            break;

        case 'Lector':
            $sqlEsp = "INSERT INTO Lector (id_lector, telefono, correo) VALUES (?, ?, ?)";
            $stmt   = $conn->prepare($sqlEsp);
            $stmt->bind_param('iss', $id_usuario, $telefono, $correo);
            break;
    }

    if (!$stmt->execute()) {
        throw new Exception('Error al insertar en tabla especializada: ' . $stmt->error);
    }

    // Confirmar transacción
    $conn->commit();

    // Mensaje de éxito
    echo "Usuario registrado exitosamente.<br>";
    echo "Código de usuario: <strong>$code_user</strong><br>";
    echo "Contraseña generada: <strong>$contrasena</strong>";

} catch (Exception $e) {
    // Deshacer transacción y mostrar error
    $conn->rollback();
    die('Error en registro: ' . $e->getMessage());
}

// Cerrar conexión
$conn->close();
?>
