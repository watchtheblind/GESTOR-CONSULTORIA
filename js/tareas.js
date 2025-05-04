$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#tareasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'tareas/tareas_datatables.php',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1, visible: false },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { 
                data: 8,
                orderable: false,
                searchable: false,
                visible: false
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });

    // Manejador para el botón de ver detalles
    $(document).on('click', '.btn-info', function() {
        var id = $(this).closest('tr').find('td:first').text();
        // Aquí puedes implementar la lógica para ver detalles
        console.log('Ver detalles de tarea:', id);
    });

    // Manejador para el botón de editar
    $(document).on('click', '.btn-warning', function() {
        var id = $(this).closest('tr').find('td:first').text();
        // Aquí puedes implementar la lógica para editar
        console.log('Editar tarea:', id);
    });

    // Manejador para el botón de eliminar
    $(document).on('click', '.btn-danger', function() {
        var id = $(this).closest('tr').find('td:first').text();
        if (confirm('¿Estás seguro de eliminar esta tarea?')) {
            $.ajax({
                url: 'tareas/eliminar_tarea.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        alert('Tarea eliminada correctamente');
                    } else {
                        alert('Error al eliminar la tarea: ' + response.error);
                    }
                },
                error: function() {
                    alert('Error al comunicarse con el servidor');
                }
            });
        }
    });
}); 