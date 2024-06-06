<?php
session_start();
require_once '../Conexion.php';

$sql = "SELECT name, email, message, created_at FROM comments";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table><tr><th>Nombre</th><th>Correo Electrónico</th><th>Mensaje</th><th>Fecha</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["name"]."</td><td>".$row["email"]."</td><td>".$row["message"]."</td><td>".$row["created_at"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "No hay comentarios";
}
$conn->close();

