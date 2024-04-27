<?php
session_start();
if (!isset($_SESSION['correo'])) {
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
  <link rel="stylesheet" href="css/Estilos.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

  <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- xlsx -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body class="bg-gray-100 text-gray-900 tracking-wider leading-normal ">

  <div class="container mx-auto px-4">

    <h1 class="text-4xl md:text-5xl text-center font-serif font-bold text-black-500 mb-6 mt-6">
      Bienvenido al Sistema de Recordatorio de Citas Médicas
    </h1>


    <div class="container mx-auto max-w-lg mt-20 text-center">
      <h2 class="text-3xl font-semibold text-gray-800 mb-8">¡Bienvenido, <?php echo $_SESSION['correo']; ?>!</h2>
      <a href="../Inicio_sesion.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Cerrar sesión</a>
    </div>

    <div class="cargaExel bg-blue-100 border-b-4 border-gray-500 rounded-lg p-4 md:p-6 shadow-md relative ">
      <label for="excelFile" class="block text-lg font-semibold mb-2">Cargar archivo Excel:</label>
      <input class="block w-full py-2 px-3 md:px-4 mb-3 md:mb-4 rounded-md border border-gray-300" type="file" id="excelFile" accept=".xls, .xlsx">
      <button onclick="cargarExcel()" class=" static block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-3 md:px-4 rounded mb-2 md:mb-0 md:mr-2">
        Cargar Excel
      </button>
      <button class="block w-full md:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-5 md:px-4 absolute bottom-6 right-6 rounded " id="guardarDatosBtn">
        Enviar Mensaje

      </button>
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
          <!-- Aquí irán tus datos -->
        </tbody>
      </table>

    </div>

  </div>

  <div class="modal fade" id="filasOmitidasModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Filas Omitidas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul id="filasOmitidasList"></ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../../js/cargar.js"></script>


  <?php
  include('../cargabd.php');
  ?>

</body>

</html>