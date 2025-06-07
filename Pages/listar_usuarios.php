<?php
// Pages/listar_usuarios.php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once __DIR__ . '/../includes/conexion.php';

// 1) Captura filtros
$tipo   = $_GET['tipo_usuario'] ?? '';
$nombre = trim($_GET['nombre'] ?? '');
$codigo = trim($_GET['codigo'] ?? '');

// 2) Construye WHERE y parámetros
$where    = [];
$params   = [];
$types    = '';

if ($tipo !== '') {
    $where[]  = 'u.tipo_usuario = ?';
    $params[] = $tipo;
    $types   .= 'i';
}

if ($nombre !== '') {
    $where[]  = '(u.nombre LIKE ? OR u.apellidoP LIKE ? OR u.apellidoM LIKE ?)';
    $params[] = "%$nombre%";
    $params[] = "%$nombre%";
    $params[] = "%$nombre%";
    $types   .= 'sss';
}

if ($codigo !== '') {
    $where[]  = 'u.code_user LIKE ?';
    $params[] = "%$codigo%";
    $types   .= 's';
}

// 3) Arma SQL
$sql = "
    SELECT
      u.id, u.code_user, u.nombre, u.apellidoP, u.apellidoM,
      CASE u.tipo_usuario
        WHEN 1 THEN 'Administrador'
        WHEN 2 THEN 'Bibliotecario'
        WHEN 3 THEN 'Lector'
      END AS tipo,
      u.fecha_nacimiento, u.fecha_registro,
      b.telefono    AS telefono_bibliotecario,
      l.telefono    AS telefono_lector,
      l.correo      AS correo_lector
    FROM Usuario u
    LEFT JOIN Bibliotecario b ON u.id = b.id_biblio
    LEFT JOIN Lector       l ON u.id = l.id_lector
";
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY u.id DESC';

// 4) Ejecuta
$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios registrados</title>
</head>
<body>
  <h1>Listado de usuarios</h1>

  <!-- Formulario de filtros -->
  <form method="get">
    <select name="tipo_usuario">
      <option value="">— Todos los tipos —</option>
      <option value="1"<?= $tipo==='1'?' selected':'' ?>>Administrador</option>
      <option value="2"<?= $tipo==='2'?' selected':'' ?>>Bibliotecario</option>
      <option value="3"<?= $tipo==='3'?' selected':'' ?>>Lector</option>
    </select>
    <input type="text" name="nombre"  placeholder="Buscar por nombre" value="<?= htmlspecialchars($nombre) ?>">
    <input type="text" name="codigo"  placeholder="Buscar por código" value="<?= htmlspecialchars($codigo) ?>">
    <button type="submit">Filtrar</button>
  </form>

  <!-- Tabla de resultados -->
  <?php if ($result->num_rows): ?>
  <table border="1" cellpadding="4" cellspacing="0">
    <tr>
      <th>ID</th><th>Código</th><th>Nombre</th><th>Apellidos</th>
      <th>Tipo</th><th>Fecha Nac.</th><th>Fecha Reg.</th>
      <th>Tel. Biblio</th><th>Tel. Lector</th><th>Correo Lector</th>
      <th>Acciones</th>
    </tr>
    <?php while ($u = $result->fetch_assoc()): ?>
      <tr>
        <td><?=htmlspecialchars($u['id'])?></td>
        <td><?=htmlspecialchars($u['code_user'])?></td>
        <td><?=htmlspecialchars($u['nombre'])?></td>
        <td><?=htmlspecialchars($u['apellidoP'].' '.$u['apellidoM'])?></td>
        <td><?=htmlspecialchars($u['tipo'])?></td>
        <td><?=htmlspecialchars($u['fecha_nacimiento'])?></td>
        <td><?=htmlspecialchars($u['fecha_registro'])?></td>
        <td><?=htmlspecialchars($u['telefono_bibliotecario'] ?? '-')?></td>
        <td><?=htmlspecialchars($u['telefono_lector']      ?? '-')?></td>
        <td><?=htmlspecialchars($u['correo_lector']        ?? '-')?></td>
        <td>
          <a href="detalles_usuario.php?id=<?=$u['id']?>">Ver</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p>No se encontraron usuarios.</p>
  <?php endif; ?>

<?php $conn->close(); ?>
</body>
</html>
