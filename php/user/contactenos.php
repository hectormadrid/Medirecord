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
              
            </li>
            <li>
            </li>
      

            <li>
                <a href="#">
                <i class='bx bx-box'></i>
                    <span class="link_name">Contactenos</span>
                </a>
               
            </li>
     
            <li>
                <div class="profile-details">

                    <div class="name-job  text-wrap overflow-hidden ">
                        <div class="profile_name  ">
                            Usuario, <?php echo $_SESSION['nombre']; ?>!</div>
                    </div>
                    <a href="../Inicio_sesion.php" class='inline-block bg-[#3664E4] hover:bg-red-800 text-white font-bold py-2 px-4 rounded mb-4  bx bx-log-out '> </a>
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
                <form id="addContactenosForm" method="POST" class="max-w-xl mx-auto bg-white p-8 shadow-md rounded">
                    <div class="mb-4">
                        <label for="nombre" class="block text-gray-700">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required readonly class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="Rut" class="block text-gray-700">Rut:</label>
                        <input type="text" id="Rut" name="Rut" value="<?php echo htmlspecialchars($rut); ?>" readonly required class="w-full px-3 py-2 border rounded">
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
    <script>
$(document).ready(function() {
    $('#addContactenosForm').submit(function(event) {
        event.preventDefault(); // Evitar que el formulario se envíe de forma tradicional
        // Obtener los valores de los campos del formulario

         var nombre = $('#nombre').val();
        var mensaje = $('#mensaje').val();
        var rut = $('#Rut').val(); 
        // Verificar si el campo está vacío
        if (mensaje.trim() === '') {
            // Mostrar un mensaje de error
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Por favor, completa todos los campos.'
            });
            return; // Detener el envío del formulario
        }

        // Si el campo no está vacío, enviar los datos al servidor
        var formData = {
          nombre: nombre,
                rut: rut,
            mensaje: mensaje
        };

        $.ajax({
            type: 'POST',
            url: 'Enviar_mensajes.php', // Ruta al script PHP que procesará los datos
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log(response); // Verificar la respuesta real del servidor
                // Mostrar un mensaje de éxito o error
                if (response.success) {
                    // Éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message
                    }).then(() => {
                        // Recargar la página
                        window.location.reload();
                    });
                } else {
                    // Error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                console.log(xhr.responseText); // Ver respuesta completa del servidor para depuración
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema con la solicitud. Inténtalo nuevamente.'
                });
            }
        });
    });
});
</script>

</body>

</html>