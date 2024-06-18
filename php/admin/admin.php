<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'admin') {
  header("Location: ../Inicio_sesion.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
          <a href="../Inicio_sesion.php" class='inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mb-4  bx bx-log-out' > </a>
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
        Bienvenido Administrador
      </h1>
      <div class="bg-white p-8 rounded shadow-md w-full max-w-4xl mx-auto">
        <h2 class="text-2xl mb-4 text-center font-semibold text-blue-700">Lista de Funcionarios</h2>
        <div class="">
          <table id="funcionariosTable" class="table-auto min-w-full">
            <thead class="bg-blue-500 text-white">
              <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Rut</th>
                <th class="px-4 py-2">Nombre</th>
                <th class="px-4 py-2">Contraseña</th>

              </tr>
            </thead>
            <tbody id="funcionariosTableBody" class="bg-blue-100 divide-y divide-blue-200">
              
            </tbody>
          </table>
        </div>
      </div>

    </div>
    <div class="bg-white p-8 rounded shadow-md w-full max-w-4xl mx-auto">
      <h2 class="text-2xl mb-4 text-center font-semibold">Agregar Nuevo Funcionario</h2>
      <form id="addFuncionarioForm" class="w-full max-w-lg mx-auto">
        <div class="mb-4">
          <label for="nombre" class="block text-gray-700 font-bold mb-2">Nombre:</label>
          <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label for="rut" class="block text-gray-700 font-bold mb-2">Rut:</label>
          <input type="text" id="rut" name="rut" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label for="pass" class="block text-gray-700 font-bold mb-2">Contraseña:</label>
          <input type="password" id="pass" name="pass" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="flex items-center justify-between">
          <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Agregar Funcionario</button>
        </div>
      </form>

    </div>

  </section>
  <script src="../../js/Menu_desplegable.js"></script>
  <script src="../../js/datatables.js"></script>
  <script src="../../js/tablaadmin.js"></script>
  <script>
    $(document).ready(function() {
      $('#addFuncionarioForm').submit(function(event) {
        event.preventDefault(); // Evitar que el formulario se envíe de forma tradicional
        // Obtener los valores de los campos del formulario
        var nombre = $('#nombre').val();
        var rut = $('#rut').val();
        var pass = $('#pass').val();

        // Verificar si los campos están vacíos
        if (nombre.trim() === '' || rut.trim() === '' || pass.trim() === '') {
          // Mostrar un mensaje de error
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Por favor, completa todos los campos.'
          });
          return; // Detener el envío del formulario
        }

        // Si los campos no están vacíos, enviar los datos al servidor
        var formData = {
          nombre: nombre,
          rut: rut,
          pass: pass
        };


      
        $.ajax({
          type: 'POST',
          url: 'agregar_funcionarios.php', // Ruta al script PHP que procesará los datos
          data: formData,
          dataType: 'json',
          success: function(response) {
            // Mostrar un mensaje de éxito o error
            if (response.success) {
              // Éxito
              alert(response.message);
              // Aquí puedes recargar la tabla DataTable o realizar otras acciones necesarias
            } else {
              // Error
              alert('Error: ' + response.message);
            }
          }
        });
      });
    });
  </script>

</body>

</html>