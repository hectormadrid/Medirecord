<?php
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: home.php");
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
      <span class="logo_name text-center" style='color:#ffffff'>Usuario</span>
    </div>
    <ul class="nav-links">
      <li>
        <a href="home.php">
          <i class='bx bx-grid-alt'></i>
          <span class="link_name">Inicio</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="#">Inicio</a></li>
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
        <a href="contactenos.php">
          <i class='bx bxs-contact'></i>
          <span class="link_name">Contactenos</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="contactenos.php">Contactenos</a></li>
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


      <div class="container mx-auto max-w-lg mt-20 text-center">
        <h2 class="text-3xl font-semibold text-gray-800 mb-8">¡Bienvenido, <?php echo $_SESSION['nombre']; ?>!</h2>
        <a href="../Inicio_sesion.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mb-4 ">Cerrar sesión</a>
      </div>

      <div class="container mx-auto px-4 relative">
        <div class="cargaExel bg-blue-100 border-b-4 border-gray-500 rounded-lg p-4 md:p-6 shadow-md ">
          <label for="excelFile" class="block text-lg font-semibold mb-2">Cargar archivo Excel:</label>
          <input class="block w-full py-2 px-3 md:px-4 mb-3 md:mb-4 rounded-md border border-gray-300" type="file" id="excelFile" accept=".xls, .xlsx">
          <button onclick="cargarExcel()" class="static block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-3 md:px-4 rounded mb-2 md:mb-0 md:mr-2">
            Cargar Excel
          </button>
          <button class="block w-full md:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-5 md:px-4 absolute  right-10 top-28 rounded" id="guardarDatosBtn">
            Enviar Mensaje
          </button>
        </div>
      </div>

      <div class="flex items-center justify-center font-sans font-bold text-center md:text-left break-normal text-indigo-500 px-2 py-4 md:py-8 text-xl md:text-2xl">
        <p>Lista de Pacientes</p>
      </div>

      <div class="overflow-x-auto">
        <table id="tabla_pacientes" class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
          <thead class="bg-blue-500 text-white">
            <tr>
              <th scope="col" class="px-6 py-3 text-left font-bold">Codigo</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Numero</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Nombre</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Rut</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Dia</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Hora</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Medico</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Especialidad</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Tipo de Consulta</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Lugar</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Recinto</th>
              <th scope="col" class="px-6 py-3 text-left font-bold">Email</th>
            </tr>
          </thead>
          <tbody class="bg-blue-100 divide-y divide-blue-200">

          </tbody>
        </table>

      </div>

      <div class="flex items-center justify-center font-sans font-bold text-center md:text-left break-normal text-indigo-500 px-2 py-4 md:py-8 text-xl md:text-2xl">
      <p>Lista de Pacientes con numero mal ingresado o no ingresado </p>
    </div>
    <button onclick="generarPDF()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold" id="generarpdf">Generar PDF</button>


    <div class="overflow-x-auto">
      <table id="tabla_filas_omitidas" class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-blue-500 text-white">
          <tr>
            <th scope="col" class="px-6 py-3 text-left font-bold">Codigo</th>
            <th scope="col" class="px-6 py-3 text-left font-bold">Número</th>
            <th scope="col" class="px-6 py-3 text-left font-bold">Nombre</th>
            <th scope="col" class="px-6 py-3 text-left font-bold">Rut</th>
            <th scope="col" class="px-6 py-3 text-left font-bold">Día</th>
            <th scope="col" class="px-6 py-3 text-left font-bold">Hora</th>
           

          </tr>
        </thead>
        <tbody class="bg-blue-100 divide-y divide-blue-200">
          <!-- Aquí se agregarán las filas omitidas de datos dinámicamente -->
        </tbody>
      </table>
    </div>

        
    </div>



  </section>
  <script src="../../js/cargar.js"></script>
  <script src="../../js/Menu_desplegable.js"></script>
  <script>
    function generarPDF() {
        // Redireccionar a tu archivo PHP que genera el PDF
   //     window.location.href = 'generarpdf.php';
    }
</script>

  <?php
  include('../cargabd.php');
  
  ?>

</body>

</html>