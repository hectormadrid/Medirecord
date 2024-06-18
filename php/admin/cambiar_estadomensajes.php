<?php
session_start();
require_once '../Conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../Inicio_sesion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $revisado = $_POST['revisado'];

    // Verificar la conexión a la base de datos
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Preparar la consulta SQL para actualizar el estado de revisión
    $sql = "UPDATE Comentarios SET revisado = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("ii", $revisado, $id);

    if ($stmt->execute()) {
        header("Location: Mensajes.php");
        exit();
    } else {
        echo "Error al actualizar el estado del comentario: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
