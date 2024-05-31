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
    zeroRecords: "Ningún Funcionario encontrado",
    info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
    infoEmpty: "Ningún Funcionario encontrado",
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
