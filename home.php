<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MediRecord</title>
  <!-- Bootstrap-->

  <!-- DataTable -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!--Css-->
  <link rel="stylesheet" href="css/Estilos.css">
  <script src="https://cdn.tailwindcss.com"></script>


</head>

<body>
  <div class="container pt-5">
    <div class="row">
      <div id="banner">
        <h1>¡Bienvenido al Sistema de Recordatorio de Citas Médicas!</h1>
      </div>
      <div class="cargaExel">
        <input type="file" id="excelFile" accept=".xls, .xlsx"><br>
        <button onclick="cargarExcel()">Cargar Excel</button>
      </div>

      <div class="col-md-12 text-center">
        <h1>Lista de Pacientes</h1>
      </div>
      <div class="col-md-12">
        <table id="tabla_pacientes" class="table table-striped" style="width:100%">
          <thead>
            <tr>
              <th scope="col" class="centered">Codigo</th>
              <th scope="col" class="centered">Numero</th>
              <th scope="col" class="centered">Nombre</th>
              <th scope="col" class="centered">Rut</th>
              <th scope="col" class="centered">Dia</th>
              <th scope="col" class="centered">Hora</th>
              <th scope="col" class="centered">Medico</th>
              <th scope="col" class="centered">Espacialidad</th>
              <th scope="col" class="centered">Tipo de Consulta</th>
              <th scope="col" class="centered">Lugar</th>
              <th scope="col" class="centered">Recinto</th>
              <th scope="col" class="centered">Email</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <button class="botonenviar" id="guardarDatosBtn">Enviar Mensaje </button>

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

  <script src="js/cargar.js"></script>


  <?php
  include('./js/cargabd.php');
  ?>
  <!-- Bootstrap-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
  <!-- jQuery -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <!-- DataTable -->
  <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
</body>

</html>