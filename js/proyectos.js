$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#proyectosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'proyectos/proyectos_datatables.php',
            type: 'POST'
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'descripcion' },
            { data: 'cliente' },
            { data: 'estado' },
            { data: 'observaciones' },
            { 
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info" onclick="editarObservaciones(${row.id}, '${row.observaciones}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });
});

// Función para editar observaciones
function editarObservaciones(id, observaciones) {
    $('#proyecto_id').val(id);
    $('#observaciones').val(observaciones);
    $('#observacionesModal').modal('show');
}

// Guardar observaciones
$('#guardarObservaciones').click(function() {
    var formData = {
        proyecto_id: $('#proyecto_id').val(),
        observaciones: $('#observaciones').val()
    };

    $.ajax({
        url: 'proyectos/acciones_proyectos.php',
        type: 'POST',
        data: {
            action: 'update_observaciones',
            ...formData
        },
        success: function(response) {
            if (response.success) {
                $('#observacionesModal').modal('hide');
                $('#proyectosTable').DataTable().ajax.reload();
                alert('Observaciones actualizadas correctamente');
            } else {
                alert('Error: ' + response.error);
            }
        },
        error: function() {
            alert('Error al comunicarse con el servidor');
        }
    });
}); 