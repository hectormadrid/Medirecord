<?php
session_start();
require_once '../Conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $rut = $_POST['Rut'];
    $mensaje = $_POST['mensaje'];

    // Verificar la conexión a la base de datos
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Preparar la consulta SQL para insertar el comentario
    $sql = "INSERT INTO Comentarios (nombre, rut, mensaje,fecha) VALUES (?, ?, ?,now())";
    $stmt = $conexion->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("sss", $nombre, $rut, $mensaje);

    if ($stmt->execute()) {
        echo "Comentario enviado exitosamente.";
        header("Location: contactenos.php");
        exit();
    } else {
        echo "Error al enviar el comentario: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
