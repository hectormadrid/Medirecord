<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: home.php");
    exit();
}

require_once '../Conexion.php';

$nombre = $_SESSION['nombre'];
$rut = "";

// Verificar la conexión a la base de datos
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Preparar la consulta SQL
$sql = "SELECT Rut FROM Funcionario WHERE Nombre = ?";
$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("s", $nombre);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $rut = $user['Rut'];
}

$stmt->close();
$conexion->close();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../componentes/logo pestaña.ico">
    <title>MediRecord</title>

    <!-- Tailwind CSS -->
    <link href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="../../css/Menu.css">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.3/dist/boxicons.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- xlsx -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

    <!-- Bootstrap JS -->


</head>

<body class="bg-gray-100 text-gray-900 tracking-wider leading-normal overflow-hidden">

    <div class="sidebar close">
        <div class="logo-details">
            <box-icon name='user-circle' color="#ffffff" class="mr-3 ml-2"></box-icon>
            <span class="logo_name text-center" style='color:#ffffff'>Usuario</span>
        </div>
        <ul class="nav-links">
            <li>
                <a href="home.php">
                    <i class='bx bx-grid-alt'></i>
                    <span class="link_name">Inicio</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="home.php">Inicio</a></li>
                </ul>
            </li>
            <li>
            </li>
            <li>
                <div class="iocn-link">
                    <a href="#">
                        <i class='bx bx-book-alt'></i>
                        <span class="link_name">Reportes</span>
                    </a>
                    <i class='bx bxs-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="#">Reportes</a></li>
                    <li><a href="#">Sub Menú 1</a></li>
                    <li><a href="#">Sub Menú 2</a></li>
                </ul>
            </li>

            <li>
                <a href="#">
                    <i class='bx bxs-contact'></i>
                    <span class="link_name">Contactenos</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="#">Contactenos</a></li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class='bx bxs-edit-location'></i>
                    <span class="link_name">Sedes</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="#">Sedes</a></li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-cog'></i>
                    <span class="link_name">Configuración</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="#">Configuración</a></li>
                </ul>
            </li>
            <li>
                <div class="profile-details">

                    <div class="name-job  text-wrap overflow-hidden ">
                        <div class="profile_name  ">
                            Usuario, <?php echo $_SESSION['nombre']; ?>!</div>
                    </div>
                    <i class='bx bx-log-out'></i>
                </div>
            </li>
        </ul>
    </div>
    <section class="home-section  overflow-y-auto ">
        <div class="home-content fixed">
            <i class='bx bx-menu '></i>
            <span class="text">Menu</span>
        </div>

        <div class="container mx-auto px-4">

            <h1 class="  text-4xl md:text-5xl text-center font-serif font-bold text-black-500 mb-6 mt-6">
                Bienvenido al Sistema de Recordatorio de Citas Médicas
            </h1>

            <div class="container mx-auto px-4 py-8">
                <h1 class="text-4xl text-center font-bold mb-6">Comentarios y Sugerencias</h1>
                <form action="Enviar_mensajes.php" method="POST" class="max-w-xl mx-auto bg-white p-8 shadow-md rounded">
                    <div class="mb-4">
                        <label for="nombre" class="block text-gray-700">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required readonly class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="Rut" class="block text-gray-700">Rut:</label>
                        <input type="text" id="Rut" name="Rut" value="<?php echo ($rut); ?>"readonly required class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="mensaje" class="block text-gray-700">Mensaje:</label>
                        <textarea id="mensaje" name="mensaje" required class="w-full px-3 py-2 border rounded" rows="4"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Enviar</button>
                </form>
            </div>


    </section>
    <script src="../../js/Menu_desplegable.js"></script>

</body>

</html>