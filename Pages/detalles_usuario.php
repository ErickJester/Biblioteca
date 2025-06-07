<?php
// detalles_usuario.php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once __DIR__ . '/../includes/conexion.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) die('ID inválido.');

$sql = "
  SELECT u.*, b.telefono AS tel_biblio, l.telefono AS tel_lector, l.correo 
  FROM Usuario u
  LEFT JOIN Bibliotecario b ON u.id = b.id_biblio
  LEFT JOIN Lector       l ON u.id = l.id_lector
  WHERE u.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i',$id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) die('Usuario no encontrado.');
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Detalles Usuario</title></head>
<body>
  <h1>Detalles de <?=htmlspecialchars($user['code_user'])?></h1>
  <ul>
    <li>Nombre completo: <?=htmlspecialchars($user['nombre'].' '.$user['apellidoP'].' '.$user['apellidoM'])?></li>
    <li>Tipo: <?=($user['tipo_usuario']==1?'Administrador':($user['tipo_usuario']==2?'Bibliotecario':'Lector'))?></li>
    <li>Fecha Nacimiento: <?=htmlspecialchars($user['fecha_nacimiento'])?></li>
    <li>Fecha Registro: <?=htmlspecialchars($user['fecha_registro'])?></li>
    <?php if ($user['tipo_usuario']==2): ?>
      <li>Teléfono: <?=htmlspecialchars($user['tel_biblio'])?></li>
    <?php elseif ($user['tipo_usuario']==3): ?>
      <li>Teléfono: <?=htmlspecialchars($user['tel_lector'])?></li>
      <li>Correo:   <?=htmlspecialchars($user['correo'])?></li>
    <?php endif; ?>
  </ul>
  <p><a href="listar_usuarios.php">← Volver al listado</a></p>
<?php $conn->close(); ?>
</body>
</html>

