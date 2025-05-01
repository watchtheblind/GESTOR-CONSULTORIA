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
            type: 'GET',
            data: { 
                action: 'get',
                usuario_id: id 
            },
            success: function(response) {
                try {
                    var usuario = JSON.parse(response);
                    
                    $('#usuario_id').val(usuario.id);
                    $('#nombre_usuario').val(usuario.nombre_usuario);
                    $('#correo_electronico').val(usuario.correo_electronico);
                    $('#rol').val(usuario.rol);
                    $('#numero_telefono').val(usuario.numero_telefono);
                    $('#descripcion').val(usuario.descripcion);
                    $('#esta_activo').val(usuario.esta_activo ? "1" : "0");
                    
                    $('#usuarioModalLabel').text('Editar Usuario');
                    $('#passwordField').hide();
                    $('#contrasena').removeAttr('required');
                    $('#usuarioModal').modal('show');
                } catch (e) {
                    console.error('Error parsing response:', e);
                    alert('Error al cargar los datos del usuario');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error al comunicarse con el servidor');
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
                        var res = JSON.parse(response);
                        
                        if (res.error) {
                            alert(res.error);
                        } else {
                            $('#usuariosTable').DataTable().ajax.reload();
                            alert('Usuario eliminado correctamente');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
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
                    var res = JSON.parse(response);
                    
                    if (res.error) {
                        alert(res.error);
                    } else {
                        $('#usuarioModal').modal('hide');
                        $('#usuariosTable').DataTable().ajax.reload();
                        alert('Usuario guardado correctamente');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    alert('Error al procesar la respuesta del servidor');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Error al comunicarse con el servidor');
            }
        });
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

    $('#recuperarForm').submit(function(e) {
        e.preventDefault();
        
        $.post('usuarios_actions.php', {
            action: 'recover',
            usuario_id: $('#recuperar_id').val(),
            nueva_contrasena: $('#nueva_contrasena').val()
        }, function(response) {
            var res = JSON.parse(response);
            
            if (res.error) {
                alert(res.error);
            } else {
                $('#recuperarModal').modal('hide');
                alert(res.message);
            }
        });
    });
});