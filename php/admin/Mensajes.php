<?php
session_start();
require_once '../Conexion.php';
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../Inicio_sesion.php");
    exit();
}
$sql = "SELECT id, nombre, rut, mensaje,fecha, revisado FROM Comentarios";
$result = $conexion->query($sql);

// Manejo de errores de la consulta
if ($result === false) {
    die("Error en la consulta SQL: " . $conexion->error);
}
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

<body class="bg-gray-100 text-gray-900"">

    <div class=" sidebar close">
    <div class="logo-details">
        <box-icon name='user-circle' color="#ffffff" class="mr-3 ml-2"></box-icon>
        <span class="logo_name text-center" style='color:#ffffff'>Administrador</span>
    </div>
    <ul class="nav-links">
        <li>
            <a href="admin.php">
                <i class='bx bx-grid-alt'></i>
                <span class="link_name">Inicio</span>
            </a>
        </li>

        <li>
            <div class="iocn-link">
                <a href="Mensajes.php">
                    <i class='bx bx-comment-dots'></i>
                    <span class="link_name">Mensajes</span>
                </a>
            </div>
        </li>

        
        <li>
            <div class="profile-details">

                <div class="name-job  text-wrap overflow-hidden ">
                    <div class="profile_name  ">
                         <?php echo $_SESSION['nombre']; ?>!</div>
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

        <div class="container mx-auto px-4 py-8">
            <h1 class="text-4xl text-center font-bold mb-6">Comentarios y Sugerencias</h1>
            <div class="bg-white p-8 rounded shadow-md">
                <?php if ($result->num_rows > 0) : ?>
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2">Nombre</th>
                                <th class="py-2">Rut</th>
                                <th class="py-2">Mensaje</th>
                                <th class="py-2">Fecha</th>
                                <th class="py-2">Estado</th>
                                <th class="py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['rut']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['mensaje']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['fecha']); ?></td>
                                    <td class="border px-4 py-2"><?php echo $row['revisado'] ? 'Revisado' : 'No revisado'; ?></td>
                                    <td class="border px-4 py-2">
                                        <form action="cambiar_estadomensajes.php" method="POST">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="revisado" value="<?php echo $row['revisado'] ? '0' : '1'; ?>">
                                            <button type="submit" class="bg-<?php echo $row['revisado'] ? 'red' : 'green'; ?>-500 text-white py-2 px-4 rounded">
                                                <?php echo $row['revisado'] ? 'Marcar como No Revisado' : 'Marcar como Revisado'; ?>
                                            </button>
                                        </form>
                                        <?php if ($row['revisado']) : ?>
                                            <form action="eliminar_comentario.php" method="POST" class="inline-block eliminar-form">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="bg-red-500 text-white py-2 px-4 m-1 rounded ml-2">Eliminar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>No hay comentarios.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <script src="../../js/Menu_desplegable.js"></script>
    <script>
        document.querySelectorAll('.eliminar-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const formElement = this;
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "No podrás revertir esta acción",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formElement.submit();
                    }
                })
            });
        });
    </script>
</body>

</html>