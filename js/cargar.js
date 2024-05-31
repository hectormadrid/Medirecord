let datatable;
let datatableInitialized = false;
const dataOpcion = {
  columnDefs: [
    {
      className: "text-center",
      defaultContent: "",
      targets: "_all",
    },
  ],
  destroy: true,
  language: {
    lengthMenu: "Mostrar _MENU_ registros por página",
    zeroRecords: "Ningún Paciente  encontrado",
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
      next: " Siguente", 
      previous: "Anterior " 
    },
  },
  paging: true,
  lengthChange: true,
  ordering: true,
  info: true,
};

const initDatatable = async () => {
  if (datatableInitialized) {
    datatable.destroy();
  }

  datatable = $("#tabla_pacientes, #tabla_filas_omitidas").DataTable(dataOpcion);
  $("#tabla_pacientes_filter input[type='search']").addClass("mt-1 px-2 py-1 w-full border rounded-md");
  $(".dataTables_paginate a").addClass("px-3 py-1 mr-2 bg-blue-500 text-white rounded hover:bg-blue-600");
  datatableInitialized = true;
};

function cargarExcel() {
  const input = document.getElementById("excelFile");
  let filasOmitidas = [];
  let primeraFila = true; // Variable de control para omitir la primera fila

  const file = input.files[0];
  if (file) {
    const reader = new FileReader();

    reader.onload = function (e) {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: "array" });

      const sheet = workbook.Sheets[workbook.SheetNames[0]];
      const dataObjects = XLSX.utils.sheet_to_json(sheet, { header: 1 });

      datatable.clear().draw();

      const columnIndex = 1;

      const filteredData = dataObjects.filter((row) => {
        if (primeraFila) {
          // Si es la primera fila (encabezado), no la validamos
          primeraFila = false;
          return false;
        }
        if (row[columnIndex] === undefined) {
          console.error("Índice de columna fuera de rango:", columnIndex);
          return false;
        }

        const cellValue = row[columnIndex].toString().trim();

        // Realizar la validación de la longitud
        if (cellValue.length !== 8) {
          filasOmitidas.push(row);
          return false;
        }

        return true;
      });

      datatable.rows.add(filteredData).draw(); // Carga los datos  filtrados en la tabla Datatables

      document.getElementById("guardarDatosBtn").addEventListener("click", function () {
        const dataToSave = datatable.rows().data().toArray(); // Obtén los datos de la tabla
        guardarDatosEnBaseDeDatos(dataToSave);
      });

      // Mostrar las filas omitidas en la ventana emergente si hay alguna
      if (filasOmitidas.length > 0) {
        mostrarFilasOmitidas(filasOmitidas);
      }
    };
 

   console.log(filasOmitidas)

    reader.readAsArrayBuffer(file);
  } else {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Seleccione un archivo de Excel valido",
    });
  }

}

function mostrarFilasOmitidas(filas) {
  const segundaDatatable = $("#tabla_filas_omitidas").DataTable(dataOpcion);

  // Limpiar la segunda DataTable antes de agregar nuevos datos
  segundaDatatable.clear().draw();

  // Agregar los datos estructurados a la segunda DataTable
  segundaDatatable.rows.add(filas).draw();
  
  document.getElementById("generarpdf").addEventListener("click", function () {
    const datospdf = segundaDatatable.rows().data().toArray();
    // Enviar los datos al servidor para generar el PDF
    enviarDatosDePacientesOmitidos(datospdf);

  });
}
window.addEventListener("load", async () => {
  await initDatatable();
});


function enviarDatosDePacientesOmitidos(data) {
  // Filtrar los datos para almacenar solo los campos deseados
  const datosFiltrados = data.map(function(paciente) {
    return [
      paciente[0],
      paciente[1],
      paciente[2],
      paciente[3],
      paciente[4],
      paciente[5]
      // Agrega aquí otros campos que desees almacenar
    ];
  });

  // Convertir los datos filtrados a JSON
  const jsonData = JSON.stringify(datosFiltrados);
  
  console.log("Datos enviados a generarpdf.php:", jsonData);

  // Enviar los datos al servidor utilizando AJAX
  $.ajax({
    type: "POST",
    url: "../user/pdftrucho.php",
    data: {
      data: jsonData,
    },
    dataType: 'json',
    success: function (response) {
      console.log(response)
      if (response) {
        // Los datos se guardaron con éxito en la base de datos
        window.location.href = 'generarpdf.php';

      } else {
        // Ocurrió un error al guarda los datos
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
    url: "../php/cargabd.php", // Nombre de tu archivo PHP de procesamiento
    data: {
      data: JSON.stringify(data),
    }, // Envía los datos en formato JSON

    success: function (response) {
      if (response === "success") {
        // Los datos se guardaron con éxito en la base de datos
        alert("Los datos se guardaron con éxito en la base de datos.");

      } else {
        // Ocurrió un error al guarda los datos
        alert(response);
      }
    },
    error: function () {
      alert("Error de comunicación con el servidor database.");
    },
  });
}
