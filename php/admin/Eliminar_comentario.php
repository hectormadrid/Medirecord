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

    // Verificar la conexión a la base de datos
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Preparar la consulta SQL para eliminar el comentario
    $sql = "DELETE FROM Comentarios WHERE id = ?";
    $stmt = $conexion->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: Mensajes.php");
        exit();
    } else {
        echo "Error al eliminar el comentario: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
