<?php
session_start();
require_once '../Conexion.php';

// Preparar y enlazar
$stmt = $conn->prepare("INSERT INTO comments (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);

// Establecer parámetros y ejecutar
$name = $_POST['nombre'];
$email = $_POST['correo'];
$message = $_POST['mensaje'];
$stmt->execute();

$stmt->close();
$conn->close();

echo "Comentario enviado exitosamente";
?>
