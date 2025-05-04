$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#tareasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'tareas/tareas_datatables.php',
            type: 'POST',
            data: function(d) {
                d.filter = $('.btn-group .active').data('filter');
            }
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
                visible: true
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });

    // Manejador para los filtros
    $('.btn-group button').click(function() {
        $('.btn-group button').removeClass('active');
        $(this).addClass('active');
        table.ajax.reload();
    });

    // Manejador para el formulario de creación
    $('#crearTareaForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'tareas/acciones_tareas.php',
            type: 'POST',
            data: formData + '&action=create',
            dataType: 'json',
            success: function(response) {
                try {
                    if (response.success) {
                        $('#crearTareaModal').modal('hide');
                        table.ajax.reload();
                        alert('Tarea creada correctamente');
                    } else {
                        alert('Error: ' + (response.error || 'Error desconocido'));
                    }
                } catch (e) {
                    console.error('Error procesando la respuesta:', e);
                    alert('Error al procesar la respuesta del servidor');
                }
            },
            error: function(xhr, status, error) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    alert('Error: ' + (response.error || 'Error desconocido'));
                } catch (e) {
                    console.error('Error en la petición:', status, error);
                    alert('Error al comunicarse con el servidor');
                }
            }
        });
    });

    // Manejador para el botón de marcar como completada
    $(document).on('click', '.btn-success', function() {
        var id = $(this).closest('tr').find('td:first').text();
        
        if (confirm('¿Estás seguro de marcar esta tarea como completada?')) {
            $.ajax({
                url: 'tareas/acciones_tareas.php',
                type: 'POST',
                data: { 
                    action: 'complete',
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        alert('Tarea marcada como completada');
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function() {
                    alert('Error al comunicarse con el servidor');
                }
            });
        }
    });

    // Manejador para el botón de editar
    $(document).on('click', '.btn-warning', function() {
        var id = $(this).closest('tr').find('td:first').text();
        
        $.ajax({
            url: 'tareas/acciones_tareas.php',
            type: 'POST',
            data: { 
                action: 'get',
                id: id
            },
            success: function(response) {
                try {
                    var tarea = response;
                    $('#tarea_id').val(tarea.id);
                    $('#descripcion').val(tarea.descripcion);
                    $('#esta_completada').prop('checked', tarea.esta_completada == 1);
                    
                    var modal = new bootstrap.Modal(document.getElementById('editarTareaModal'));
                    modal.show();
                } catch (e) {
                    console.error('Error al procesar los datos:', e);
                    alert('Error al cargar los datos de la tarea');
                }
            },
            error: function() {
                alert('Error al comunicarse con el servidor');
            }
        });
    });

    // Manejador para el formulario de edición
    $('#editarTareaForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize() + '&action=update';
        
        $.ajax({
            url: 'tareas/acciones_tareas.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#editarTareaModal').modal('hide');
                    table.ajax.reload();
                    alert('Tarea actualizada correctamente');
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('Error al comunicarse con el servidor');
            }
        });
    });

    // Manejador para el botón de eliminar
    $(document).on('click', '.btn-danger', function() {
        var id = $(this).closest('tr').find('td:first').text();
        if (confirm('¿Estás seguro de eliminar esta tarea?')) {
            $.ajax({
                url: 'tareas/acciones_tareas.php',
                type: 'POST',
                data: { 
                    action: 'delete',
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        alert('Tarea eliminada correctamente');
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function() {
                    alert('Error al comunicarse con el servidor');
                }
            });
        }
    });
}); 