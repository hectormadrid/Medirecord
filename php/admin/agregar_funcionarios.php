<?php
session_start();
require_once '../Conexion.php';

// Verificar si el usuario es un administrador
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../Inicio_sesion.php");
    exit();
}

$nombre = $_POST['nombre'];
$rut = $_POST['rut'];
$pass = $_POST['pass'];

// Crear la consulta SQL para agregar el funcionario a la base de datos
$sql = "INSERT INTO Funcionario (nombre, rut, pass) VALUES ('$nombre', '$rut', '$pass')";

// Ejecutar la consulta
if (mysqli_query($conexion, $sql)) {
    echo json_encode(array('success' => true, 'message' => 'Funcionario agregado correctamente'));
} else {
    echo json_encode(array('success' => false, 'message' => 'Error al agregar funcionario: ' . mysqli_error($conexion)));
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
