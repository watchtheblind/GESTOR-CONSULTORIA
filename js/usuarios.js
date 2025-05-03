$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#usuariosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'usuarios_datatables.php',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { 
                data: 6,
                orderable: false,
                searchable: false
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });

    // Inicializar DataTable de Consultores
    var consultoresTable = $('#consultoresTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'consultores_datatables.php',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { 
                data: 6,
                orderable: false,
                searchable: false
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });

    // Inicializar DataTable de Clientes
    var clientesTable = $('#clientesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'clientes_datatables.php',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { 
                data: 6,
                orderable: false,
                searchable: false
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });

    // Mostrar modal para nuevo usuario
    $('#usuarioModal').on('show.bs.modal', function() {
        $('#usuarioForm')[0].reset();
        $('#usuario_id').val('');
        $('#usuarioModalLabel').text('Nuevo Usuario');
        $('#passwordField').show();
        $('#contrasena').attr('required', true);
    });

    // En tu archivo usuarios.js

    $(document).on('click', '.editarUsuario', function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'usuarios_actions.php',
            type: 'POST',
            data: { 
                action: 'get',
                usuario_id: id 
            },
            success: function(response) {
                try {
                    var usuario = response; // jQuery parsea automáticamente JSON si el header es correcto
                    console.log(usuario); // Para depurar y verificar la respuesta
                    
                    // Asignar valores a los campos del modal
                    $('#editarUsuarioForm #usuario_id').val(usuario.id);
                    $('#editarUsuarioForm #nombre_usuario').val(usuario.nombre_usuario);
                    $('#editarUsuarioForm #correo_electronico').val(usuario.correo_electronico);
                    $('#editarUsuarioForm #rol').val(usuario.rol);
                    $('#editarUsuarioForm #numero_telefono').val(usuario.numero_telefono);
                    $('#editarUsuarioForm #descripcion').val(usuario.descripcion);
                    $('#editarUsuarioForm #esta_activo').val(usuario.esta_activo ? "1" : "0");
                    
                    // Ocultar campo contraseña y no requerirlo
                    $('#editarUsuarioForm #passwordField').hide();
                    $('#editarUsuarioForm #contrasena').removeAttr('required');

                    // Cambiar título del modal
                    $('#editarUsuarioModalLabel').text('Editar Datos del Usuario');

                    // Mostrar el modal (Bootstrap 5)
                    var editarModal = new bootstrap.Modal(document.getElementById('editarUsuarioModal'));
                    editarModal.show();
                    
                } catch (e) {
                    console.error('Error procesando los datos del usuario:', e);
                    alert('Error al cargar los datos del usuario');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error al comunicarse con el servidor');
            }
        });
    });

    // Manejo del envío del formulario (modificar según tu lógica de guardado)
    $('#editarUsuarioForm').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var action = 'update'; // Como es modal de editar, se asume update

        $.ajax({
            url: 'usuarios_actions.php',
            type: 'POST',
            data: formData + '&action=' + action,
            success: function(response) {
                try {
                    if(response.error){
                        alert(response.error);
                    } else {
                        alert('Usuario actualizado correctamente');
                        $('#editarUsuarioModal').modal('hide');
                        $('#usuariosTable').DataTable().ajax.reload();
                    }
                } catch(e) {
                    console.error('Error procesando la respuesta:', e);
                    alert('Error al procesar la respuesta del servidor');
                }
            },
            error: function(xhr, status, error) {
                alert('Error en la comunicación con el servidor');
            }
        });

    });
    
    $(document).on('click', '.eliminarUsuario', function() {
        if (confirm('¿Estás seguro de eliminar este usuario?')) {
            var id = $(this).data('id');
            
            $.ajax({
                url: 'usuarios_actions.php',
                type: 'POST',
                data: { 
                    action: 'delete', 
                    usuario_id: id 
                },
                success: function(response) {
                    try {
                        // No uses JSON.parse si la respuesta ya es un objeto
                        if (response.error) {
                            alert(response.error);
                        } else {
                            $('#usuariosTable').DataTable().ajax.reload();
                            alert('Usuario eliminado correctamente');
                        }
                    } catch (e) {
                        console.error('Error procesando la respuesta:', e);
                        alert('Error al procesar la respuesta');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Error al comunicarse con el servidor');
                }
            });
        }
    });
    // Guardar usuario
    $('#usuarioForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var action = $('#usuario_id').val() ? 'update' : 'create';
        
        $.ajax({
            url: 'usuarios_actions.php',
            type: 'POST',
            data: formData + '&action=' + action,
            success: function(response) {
                try {
                    // response is expected as a JSON object parsed by jQuery automatically
                    if (response.error) {
                        alert(response.error);
                    } else {
                        $('#usuarioModal').modal('hide');
                        $('#usuariosTable').DataTable().ajax.reload();
                        alert('Usuario guardado correctamente');
                    }
                } catch (e) {
                    console.error('Error procesando la respuesta:', e);
                    alert('Error al procesar la respuesta del servidor');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error al comunicarse con el servidor');
            }
        });
    });

    $(document).on('click', '.recuperarContrasena', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');

        // Asignar datos si lo deseas
        $('#recuperar_id').val(id);
        $('#usuarioNombre').text(nombre);

        // Mostrar el modal manualmente
        var modal = new bootstrap.Modal(document.getElementById('recuperarModal'));
        modal.show();
    });

   // Recuperar contraseña
    $('#recuperarModal').on('show.bs.modal', function(e) {
        var button = $(e.relatedTarget);
        var id = button.data('id');
        var nombre = button.data('nombre');
        
        $('#recuperar_id').val(id);
        $('#usuarioNombre').text(nombre);
        
        // Generar contraseña aleatoria
        var newPassword = Math.random().toString(36).slice(-8);
        $('#nueva_contrasena').val(newPassword);
    });

    // Manejo del formulario de recuperación
    $('#recuperarForm').submit(function(e) {
        e.preventDefault(); // Evita el comportamiento por defecto del formulario

        $.post('usuarios_actions.php', {
            action: 'recover',
            usuario_id: $('#recuperar_id').val(),
            nueva_contrasena: $('#nueva_contrasena').val()
        }, function(response) {
            if (response.error) {
                alert(response.error);
            } else {
                $('#recuperarModal').modal('hide');
                alert(response.message);
            }
        });
    });

    // Manejadores de eventos para los botones de consultores
    $(document).on('click', '.asignarCliente', function() {
        var id = $(this).data('id');
        $('#consultorId').val(id);
        $('#asignarClienteModal').modal('show');
    });

    $(document).on('click', '.retirarCliente', function() {
        var id = $(this).data('id');
        $('#retirarConsultorId').val(id);
        $('#retirarClienteModal').modal('show');
    });

    // Manejar el envío del formulario de retirar cliente
    $('#retirarClienteForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'retirar_cliente',
            consultor_id: $('#retirarConsultorId').val(),
            cliente_id: $('#selectClientesAsignados').val()
        };

        $.ajax({
            url: 'usuarios_actions.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#retirarClienteModal').modal('hide');
                    $('#consultoresTable').DataTable().ajax.reload();
                    alert('Cliente retirado correctamente');
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                alert('Error: ' + response.error);
            }
        });
    });

    $(document).on('click', '.enviarMensaje', function() {
        var id = $(this).data('id');
        $('#mensajeConsultorId').val(id);
        $('#mensajeModal').modal('show');
    });

    $(document).on('click', '.asignarTarea', function() {
        var id = $(this).data('id');
        $('#tareaConsultorId').val(id);
        $('#tareaModal').modal('show');
    });

    $(document).on('click', '.gestionarColaborador', function() {
        var id = $(this).data('id');
        $('#consultorPrincipalId').val(id);
        $('#colaboradoresModal').modal('show');
    });

    // Manejadores de eventos para los botones de clientes
    $(document).on('click', '.mensajeCliente', function() {
        var id = $(this).data('id');
        var nombre = $(this).closest('tr').find('td:eq(1)').text(); // Obtener el nombre del cliente
        
        // Guardar el ID del cliente en el modal
        $('#mensajesClienteModal').data('cliente-id', id);
        $('#mensajesClienteModalLabel').text('Opciones de Mensajes - ' + nombre);
        
        // Mostrar el modal
        var modal = new bootstrap.Modal(document.getElementById('mensajesClienteModal'));
        modal.show();
    });

    // Manejador para el botón "Ir al Chat"
    $('#irAlChat').click(function() {
        var clienteId = $('#mensajesClienteModal').data('cliente-id');
        // Aquí se implementará la redirección al chat
        alert('Redirigiendo al chat del cliente ID: ' + clienteId);
    });

    // Manejador para el botón "Eliminar Chat"
    $('#eliminarChat').click(function() {
        var clienteId = $('#mensajesClienteModal').data('cliente-id');
        if (confirm('¿Estás seguro de eliminar el chat con este cliente?')) {
            // Aquí se implementará la eliminación del chat
            alert('Chat eliminado para el cliente ID: ' + clienteId);
        }
    });

    $(document).on('click', '.archivosCliente', function() {
        var id = $(this).data('id');
        // Aquí se implementará la lógica para archivos
        alert('Funcionalidad de archivos pendiente para el cliente ID: ' + id);
    });

    $(document).on('click', '.proyectosCliente', function() {
        var id = $(this).data('id');
        // Aquí se implementará la lógica para proyectos
        alert('Funcionalidad de proyectos pendiente para el cliente ID: ' + id);
    });
});