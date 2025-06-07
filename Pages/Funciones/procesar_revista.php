<?php
include('../../includes/conexion.php');

$titulo = $_POST['titulo'];
$autor = $_POST['autor'];
$fecha = $_POST['fecha_registro'];
$cantidad = $_POST['cantidad'];
$genero = $_POST['genero'];
$edicion = $_POST['edicion'];

$conn->begin_transaction();

try {
    $stmt1 = $conn->prepare("INSERT INTO Material (titulo, autor, fecha_registro, cantidad, genero) VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssds", $titulo, $autor, $fecha, $cantidad, $genero);
    $stmt1->execute();
    $id_material = $conn->insert_id;

    $stmt2 = $conn->prepare("INSERT INTO Revista (id_revista, edicion) VALUES (?, ?)");
    $stmt2->bind_param("is", $id_material, $edicion);
    $stmt2->execute();

    $conn->commit();
    echo "Revista registrada correctamente.";
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
?>
