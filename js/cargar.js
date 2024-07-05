const dataOption = {
  // Opciones de configuración para DataTables
  columnDefs: [
    {
      className: "text-center",
      defaultContent: "",
      targets: "_all",
    },
  ],
  destroy: true,
  language: {
    // Configuración de idioma y mensajes
    lengthMenu: "Mostrar _MENU_ registros por página",
    zeroRecords: "Ningún Paciente encontrado",
    info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
    infoEmpty: "Ningún Paciente encontrado",
    infoFiltered: "(filtrados desde _MAX_ registros totales)",
    search: "Buscar:",
    searchPanes: {
      clearMessage: 'Limpiar búsqueda'
    },
    loadingRecords: "Cargando...",
    paginate: {
      first: "Primera",
      last: "Última",
      next: "Siguiente",
      previous: "Anterior"
    },
  },
  paging: true,
  lengthChange: true,
  ordering: true,
  info: true,
};

let datatable;
let datatableInitialized = false;

const initDatatable = async () => {
  // Inicializar o reinicializar DataTable si ya está inicializado
  if (datatableInitialized) {
    datatable.destroy();
  }

  // Inicializar DataTable en las tablas especificadas
  datatable = $("#tabla_pacientes, #tabla_filas_omitidas").DataTable(dataOption);

  // Personalizar la apariencia de los filtros de búsqueda y la paginación
  $("#tabla_pacientes_filter input[type='search']").addClass("mt-1 px-2 py-1 w-full border rounded-md");
  $(".dataTables_paginate a").addClass("px-3 py-1 mr-2 bg-blue-500 text-white rounded hover:bg-blue-600");

  datatableInitialized = true;
};

function cargarExcel() {
  const input = document.getElementById("excelFile");
  let filasOmitidas = [];
  let primeraFila = true;

  const file = input.files[0];
  if (file) {
    const reader = new FileReader();

    reader.onload = function (e) {
      try {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: "array" });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const dataObjects = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        datatable.clear().draw();

        const columnIndex = 1; // Ajusta según el índice de la columna que contiene el número del paciente

        const filteredData = dataObjects.filter((row, index) => {
          if (primeraFila) {
            primeraFila = false; // Omitir la primera fila que es el encabezado
            return false;
          }

          // Asegúrate de que la fila tenga suficientes columnas antes de acceder a columnIndex
          if (row.length <= columnIndex) {
            console.warn(`Índice de columna ${columnIndex} fuera de rango en la fila ${index + 1}`);
            filasOmitidas.push(row);
            return false;
          }

          const cellValue = row[columnIndex].toString().trim();

          // Validación: Solo aceptar filas con números de 8 caracteres
          if (cellValue.length !== 8) {
            filasOmitidas.push(row);
            return false;
          }

          return true;
        });

        // Agregar solo los datos filtrados a la tabla principal
        datatable.rows.add(filteredData).draw();

        // Mostrar las filas omitidas en la tabla de filas omitidas
        mostrarFilasOmitidas(filasOmitidas);

        // Agregar event listener para guardar datos al hacer clic en el botón
        document.getElementById("guardarDatosBtn").addEventListener("click", function () {
          const dataToSave = datatable.rows().data().toArray();
          guardarDatosEnBaseDeDatos(dataToSave);
        });
      } catch (error) {
        console.error("Error al procesar el archivo Excel:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Ocurrió un error al procesar el archivo Excel. Verifica el formato y contenido del archivo.",
        });
      }
    };

    reader.readAsArrayBuffer(file);
  } else {
    // Mostrar mensaje de error si no se selecciona un archivo válido
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Seleccione un archivo de Excel válido",
    });
  }
}
function mostrarFilasOmitidas(filas) {
  const segundaDatatable = $("#tabla_filas_omitidas").DataTable(dataOption);

  segundaDatatable.clear().draw();
  segundaDatatable.rows.add(filas).draw();

  document.getElementById("generarpdf").addEventListener("click", function () {
    const datospdf = segundaDatatable.rows().data().toArray();
    enviarDatosDePacientesOmitidos(datospdf);
  });
}

window.addEventListener("load", async () => {
  await initDatatable();
});

function enviarDatosDePacientesOmitidos(data) {
  const datosFiltrados = data.map(paciente => [
    paciente[0],
    paciente[1],
    paciente[2],
    paciente[3],
    paciente[4],
    paciente[5]
    // Agrega otros campos si es necesario
  ]);

  const jsonData = JSON.stringify(datosFiltrados);

  console.log("Datos enviados a generarpdf.php:", jsonData);

  $.ajax({
    type: "POST",
    url: "../user/pdftrucho.php",
    data: { data: jsonData },
    dataType: "json",
    success: function (response) {
      if (response) {
        window.location.href = 'generarpdf.php';
      } else {
        alert(response);
      }
    },
    error: function () {
      alert("Error de comunicación con el servidor database.");
    },
  });
}

function guardarDatosEnBaseDeDatos(data) {
  $.ajax({
    type: "POST",
    url: "cargabd.php",
    data: { data: JSON.stringify(data) },
    success: function (response) {
      if (response === "success") {
        alert("Los datos se guardaron con éxito en la base de datos.");
      } else {
        alert(response);
      }
    },
    error: function () {
      alert("Error de comunicación con el servidor database.");
    },
  });
}
