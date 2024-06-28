<?php
session_start();
require_once '../Conexion.php';

// Verificar si el usuario es un administrador
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../Inicio_sesion.php");
    exit();
}

// Establecer el encabezado Content-Type para la respuesta JSON
header('Content-Type: application/json');

$nombre = $_POST['nombre'];
$rut = $_POST['rut'];
$pass = $_POST['pass'];

// Preparar la consulta SQL para agregar el funcionario a la base de datos
$stmt = $conexion->prepare("INSERT INTO Funcionario (nombre, rut, pass) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nombre, $rut, $pass);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(array('success' => true, 'message' => 'Funcionario agregado con éxito.'));
} else {
    echo json_encode(array('success' => false, 'message' => 'Error al agregar funcionario: ' . $stmt->error));
}

// Cerrar la declaración y la conexión a la base de datos
$stmt->close();
$conexion->close();
?>
