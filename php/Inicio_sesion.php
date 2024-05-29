<?php
session_start();
require_once 'Conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"]) || empty($_POST["password"])) {
        $error = "Por favor, completa todos los campos.";
    } else {
        $name = $_POST["name"];
        $password = $_POST["password"];

        $sql = "SELECT * FROM Funcionario WHERE nombre = '$name' AND pass = '$password'";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            if ($usuario['rol'] == 'admin') {
                header("Location: admin/admin.php");
            } else {
                header("Location: user/home.php");
            }
            exit();
        } else {
            $error = "Nombre o contraseña incorrectos";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediRecord</title>
    <link href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="flex justify-center items-center h-screen">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">

            <h2 class="text-2xl mb-4 text-center font-semibold">Iniciar sesión</h2>

            <form action="Inicio_sesion.php" method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-600">Nombre</label>
                    <input type="name" name="name" id="name" class="mt-1 p-2 w-full border rounded-md">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-600">Contraseña</label>
                    <input type="password" name="password" id="password" class="mt-1 p-2 w-full border rounded-md">
                </div>

                <div>
                    <button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white rounded-md hover:bg-blue-600">Iniciar
                        sesión</button>
                </div>
            </form>

            <?php if (isset($error)) { ?>
                <p class="text-red-500 text-center"><?php echo $error; ?></p>
            <?php } ?>

        </div>
    </div>

</body>

</html>