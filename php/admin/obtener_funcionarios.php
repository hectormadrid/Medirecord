<?php
session_start();
require_once '../Conexion.php';

// Verificar si el usuario es un administrador
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../Inicio_sesion.php");
    exit();
}

$sql = "SELECT ID,rut, nombre, pass  FROM Funcionario";
$resultado = $conexion->query($sql);

$funcionarios = [];
while ($row = $resultado->fetch_assoc()) {
    $funcionarios[] = $row;
}

// Depurar el JSON
header('Content-Type: application/json');
echo json_encode($funcionarios);

