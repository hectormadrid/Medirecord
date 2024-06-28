<?php
session_start();
require_once '../Conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['nombre'])) {
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'No está autenticado.'));
    exit();
}

// Establecer el encabezado Content-Type para la respuesta JSON
header('Content-Type: application/json');

$mensaje = $_POST['mensaje'];

// Validar los datos del formulario
if (empty($mensaje)) {
    echo json_encode(array('success' => false, 'message' => 'El mensaje no puede estar vacío.'));
    exit();
}

// Aquí puedes agregar la lógica para guardar el mensaje en tu base de datos
$sql = "INSERT INTO Comentarios (nombre, rut, mensaje) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sss", $_SESSION['nombre'], $_SESSION['rut'], $mensaje);

if ($stmt->execute()) {
    echo json_encode(array('success' => true, 'message' => 'Mensaje enviado con éxito.'));
} else {
    echo json_encode(array('success' => false, 'message' => 'Error al enviar el mensaje: ' . $stmt->error));
}

// Cerrar la declaración y la conexión a la base de datos
$stmt->close();
$conexion->close();
?>
