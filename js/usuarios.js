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

    // Editar usuario
    $(document).on('click', '.editarUsuario', function() {
        var id = $(this).data('id');
        
        $.get('includes/usuarios_actions.php', { action: 'get', usuario_id: id }, function(response) {
            var usuario = JSON.parse(response);
            
            $('#usuario_id').val(usuario.id);
            $('#nombre_usuario').val(usuario.nombre_usuario);
            $('#correo_electronico').val(usuario.correo_electronico);
            $('#rol').val(usuario.rol);
            $('#numero_telefono').val(usuario.numero_telefono);
            $('#descripcion').val(usuario.descripcion);
            $('#esta_activo').val(usuario.esta_activo);
            
            $('#usuarioModalLabel').text('Editar Usuario');
            $('#passwordField').hide();
            $('#contrasena').removeAttr('required');
            $('#usuarioModal').modal('show');
        });
    });

    // Guardar usuario
    $('#usuarioForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var action = $('#usuario_id').val() ? 'update' : 'create';
        
        $.post('includes/usuarios_actions.php', formData + '&action=' + action, function(response) {
            var res = JSON.parse(response);
            
            if (res.error) {
                alert(res.error);
            } else {
                $('#usuarioModal').modal('hide');
                table.ajax.reload();
                alert('Usuario guardado correctamente');
            }
        });
    });

    // Eliminar usuario
    $(document).on('click', '.eliminarUsuario', function() {
        if (confirm('¿Estás seguro de eliminar este usuario?')) {
            var id = $(this).data('id');
            
            $.post('includes/usuarios_actions.php', { 
                action: 'delete', 
                usuario_id: id 
            }, function(response) {
                var res = JSON.parse(response);
                
                if (res.error) {
                    alert(res.error);
                } else {
                    table.ajax.reload();
                    alert('Usuario eliminado correctamente');
                }
            });
        }
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
        
        $.post('includes/usuarios_actions.php', {
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