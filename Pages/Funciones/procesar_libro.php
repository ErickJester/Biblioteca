<?php
include('../../includes/conexion.php');

$titulo = $_POST['titulo'];
$autor = $_POST['autor'];
$fecha = $_POST['fecha_registro'];
$cantidad = $_POST['cantidad'];
$genero = $_POST['genero'];
$editorial = $_POST['editorial'];
$edicion = $_POST['edicion'];
$calificacion = $_POST['calificacion'] ?? NULL;

$conn->begin_transaction();

try {
    $stmt1 = $conn->prepare("INSERT INTO Material (titulo, autor, fecha_registro, cantidad, genero) VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssds", $titulo, $autor, $fecha, $cantidad, $genero);
    $stmt1->execute();
    $id_material = $conn->insert_id;

    $stmt2 = $conn->prepare("INSERT INTO Libro (id_libro, edicion, editorial, calificacion) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("isss", $id_material, $edicion, $editorial, $calificacion);
    $stmt2->execute();

    $conn->commit();
    echo "Libro registrado correctamente.";
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
?>
