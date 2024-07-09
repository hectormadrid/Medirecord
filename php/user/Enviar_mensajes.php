<?php
session_start();
require_once '../Conexion.php';

// Establecer el encabezado Content-Type para la respuesta JSON
header('Content-Type: application/json');

// Función para manejar errores y excepciones
function handleError($message) {
    echo json_encode(array('success' => false, 'message' => $message));
    exit();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['nombre'])) {
    handleError('No está autenticado.');
}

$nombre = $_POST['nombre'] ?? '';
$rut = $_POST['rut'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

// Validar los datos del formulario
if (empty($mensaje)) {
    handleError('El mensaje no puede estar vacío.');
}

// Verificar la conexión a la base de datos
if ($conexion->connect_error) {
    handleError('Error de conexión: ' . $conexion->connect_error);
}

// Insertar el mensaje en la base de datos
$sql = "INSERT INTO Comentarios (nombre, rut, mensaje) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
if ($stmt === false) {
    handleError('Error en la preparación de la consulta: ' . $conexion->error);
}

$stmt->bind_param("sss", $nombre, $rut, $mensaje);

if ($stmt->execute()) {
    echo json_encode(array('success' => true, 'message' => 'Mensaje enviado con éxito.'));
} else {
    handleError('Error al enviar el mensaje: ' . $stmt->error);
}

// Cerrar la declaración y la conexión a la base de datos
$stmt->close();
$conexion->close();
?>
