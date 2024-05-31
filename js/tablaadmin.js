// main.js
$(document).ready(function () {
    $.ajax({
        url: 'obtener_funcionarios.php',
        dataType: 'json',
        success: function (data) {
            // Combinar la configuración global dataOpcion con la configuración específica
            var opcionesDataTable = $.extend({}, dataOpcion, {
                data: data,
                columns: [{
                    data: 'id'
                },
                {
                    data: 'rut'
                },
                {
                    data: 'nombre'
                },
                {
                    data: 'pass'
                }
                ]
                // Otras configuraciones de DataTables aquí...
            });

            // Inicializar la DataTable con las opciones combinadas
            $('#funcionariosTable').DataTable(opcionesDataTable);
        }
    });
});
