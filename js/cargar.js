let datatable;
let datatableInitialized = false;

const dataOpcion = {
  columnDefs: [
    {
      defaultContent: "",
      targets: "_all",
    },
  ],
  destroy: true,
  language: {
    lengthMenu: "Mostrar _MENU_ registros por página",
    zeroRecords: "Ningún usuario encontrado",
    info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
    infoEmpty: "Ningún usuario encontrado",
    infoFiltered: "(filtrados desde _MAX_ registros totales)",
    search: "Buscar:",
    loadingRecords: "Cargando...",
    paginate: {
      first: "Primero",
      last: "Último",
      next: "Siguiente",
      previous: "Anterior",
      
    },
  },
};

const initDatatable = async () => {
  if (datatableInitialized) {
    datatable.destroy();
  }

  datatable = $("#tabla_pacientes").DataTable(dataOpcion);
  setTimeout(function() {
    $("#tabla_pacientes_filter input[type='search']").addClass("mt-1 px-2 py-1 w-full border rounded-md");
    $(".dataTables_paginate a").addClass("px-3 py-1 mr-2 bg-blue-500 text-white rounded hover:bg-blue-600");
  }, 100);
  datatableInitialized = true;
};

function cargarExcel() {
  const input = document.getElementById("excelFile");
  const filasOmitidas = [];
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
  filas.sort((filaA, filaB) => {
    const numeroA = filaA[1];
    const numeroB = filaB[1];
    return numeroA - numeroB; // Ordenar de menor a mayor
  });

  const filasOmitidasList = $("#filasOmitidasList");
  filasOmitidasList.empty();

  // Crear un documento HTML imprimible
  const printWindow = window.open("", "", "width=600,height=600");
  printWindow.document.open();
  printWindow.document.write("<html><head><title></title></head><body>");
  printWindow.document.write("<h1>Pacientes con numero mal ingresado o no ingresado</h1>");

  // Agregar solo la columna de las filas omitidas al documento
  filas.forEach(function (fila, index) {
    const cellValue = fila[1].toString().trim();
    const cellValue1 = fila[2];
    const cellValue2 = fila[3];
    const cellValue3 = fila[4];
    const cellValue4 = fila[5];

    const value1 = cellValue.length === 0 ? "{sin número}" : cellValue;


    // Cambia el índice de fila[] al índice de la columna que deseas imprimir
    const listItem = `<li>Fila ${index + 1 } Numero: ${value1} ---Nombre: ${cellValue1} ---Rut: ${cellValue2} ---Dia: ${cellValue3} ---Hora: ${cellValue4} </li>`;
    filasOmitidasList.append(listItem);
    printWindow.document.write(
      `<p> Numero: ${value1}, Nombre: ${cellValue1}, Rut: ${cellValue2}, Dia: ${cellValue3}, Hora: ${cellValue4}</p>`
    );
  });

  printWindow.document.write("</body></html>");
  printWindow.document.close();

  // Imprimir el documento
  printWindow.print();
  printWindow.close();
}

window.addEventListener("load", async () => {
  await initDatatable();
});


function guardarDatosEnBaseDeDatos(data) {
  $.ajax({
    type: "POST",
    url: "js/cargabd.php", // Nombre de tu archivo PHP de procesamiento
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
      alert("Error de comunicación con el servidor.");
    },
  });
}
