$(document).ready(function() {
    // Cargar consultores en el select
    cargarConsultores();
    
    // Evento cuando se selecciona un consultor
    $('#selectConsultores').change(function() {
        const consultorId = $(this).val();
        if (consultorId) {
            cargarInformacionConsultor(consultorId);
            cargarClientesAsignados(consultorId);
            cargarColaboradores(consultorId);
            $('#consultorInfo').removeClass('d-none');
        } else {
            $('#consultorInfo').addClass('d-none');
        }
    });
    
    // Botón para asignar cliente
    $('#btnAsignarCliente').click(function() {
        const consultorId = $('#selectConsultores').val();
        if (consultorId) {
            $('#consultorId').val(consultorId);
            cargarClientesDisponibles();
            $('#asignarClienteModal').modal('show');
        } else {
            alert('Por favor seleccione un consultor primero');
        }
    });
    
    // Botón para retirar cliente
    $('#btnRetirarCliente').click(function() {
        // Implementar lógica para retirar cliente seleccionado
        alert('Funcionalidad para retirar cliente será implementada');
    });
    
    // Botón para enviar mensaje
    $('#btnEnviarMensaje').click(function() {
        const consultorId = $('#selectConsultores').val();
        if (consultorId) {
            $('#mensajeConsultorId').val(consultorId);
            $('#mensajeModal').modal('show');
        } else {
            alert('Por favor seleccione un consultor primero');
        }
    });
    
    // Botón para asignar tarea
    $('#btnAsignarTarea').click(function() {
        const consultorId = $('#selectConsultores').val();
        if (consultorId) {
            $('#tareaConsultorId').val(consultorId);
            // Establecer fecha mínima como hoy
            $('#fechaVencimiento').attr('min', new Date().toISOString().split('T')[0]);
            $('#tareaModal').modal('show');
        } else {
            alert('Por favor seleccione un consultor primero');
        }
    });
    
    // Botón para gestionar colaboradores
    $('#btnGestionarColaboradores').click(function() {
        const consultorId = $('#selectConsultores').val();
        if (consultorId) {
            $('#consultorPrincipalId').val(consultorId);
            cargarConsultoresColaboradores(consultorId);
            $('#colaboradoresModal').modal('show');
        } else {
            alert('Por favor seleccione un consultor primero');
        }
    });
    
    // Formulario para asignar cliente
    $('#asignarClienteForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'ajax/gestion_consultores.php',
            type: 'POST',
            data: formData + '&action=asignar_cliente',
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Cliente asignado correctamente');
                    $('#asignarClienteModal').modal('hide');
                    const consultorId = $('#selectConsultores').val();
                    cargarClientesAsignados(consultorId);
                } else {
                    alert('Error: ' + data.message);
                }
            }
        });
    });
    
    // Formulario para gestionar colaboradores
    $('#colaboradoresForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'ajax/gestion_consultores.php',
            type: 'POST',
            data: formData + '&action=gestionar_colaborador',
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert(data.message);
                    $('#colaboradoresModal').modal('hide');
                    const consultorId = $('#selectConsultores').val();
                    cargarColaboradores(consultorId);
                } else {
                    alert('Error: ' + data.message);
                }
            }
        });
    });
    
    // Formulario para enviar mensaje
    $('#mensajeForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'ajax/gestion_consultores.php',
            type: 'POST',
            data: formData + '&action=enviar_mensaje',
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Mensaje enviado correctamente');
                    $('#mensajeModal').modal('hide');
                    $('#mensajeForm')[0].reset();
                } else {
                    alert('Error: ' + data.message);
                }
            }
        });
    });
    
    // Formulario para asignar tarea
    $('#tareaForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'ajax/gestion_consultores.php',
            type: 'POST',
            data: formData + '&action=asignar_tarea',
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Tarea asignada correctamente');
                    $('#tareaModal').modal('hide');
                    $('#tareaForm')[0].reset();
                } else {
                    alert('Error: ' + data.message);
                }
            }
        });
    });
});

function cargarConsultores() {
    $.ajax({
        url: 'ajax/gestion_consultores.php',
        type: 'GET',
        data: { action: 'listar_consultores' },
        success: function(response) {
            const data = JSON.parse(response);
            const select = $('#selectConsultores');
            select.empty().append('<option value="">Seleccione un consultor</option>');
            
            data.forEach(consultor => {
                select.append(`<option value="${consultor.id}">${consultor.nombre} (${consultor.rol})</option>`);
            });
        }
    });
}

function cargarInformacionConsultor(consultorId) {
    $.ajax({
        url: 'ajax/gestion_consultores.php',
        type: 'GET',
        data: { action: 'obtener_consultor', id: consultorId },
        success: function(response) {
            const data = JSON.parse(response);
            $('#consultorNombre').text(data.nombre);
            $('#consultorEmail').text(data.email);
            $('#consultorRol').text(data.rol);
        }
    });
}

function cargarClientesAsignados(consultorId) {
    $('#clientesAsignadosTable').DataTable().destroy();
    
    $('#clientesAsignadosTable').DataTable({
        ajax: {
            url: 'ajax/gestion_consultores.php',
            type: 'GET',
            data: { action: 'listar_clientes_asignados', consultor_id: consultorId },
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'email' },
            { data: 'fecha_asignacion' },
            { 
                data: null,
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-danger btn-retirar-cliente" data-id="${row.id}">Retirar</button>`;
                }
            }
        ]
    });
}

function cargarColaboradores(consultorId) {
    $('#colaboradoresTable').DataTable().destroy();
    
    $('#colaboradoresTable').DataTable({
        ajax: {
            url: 'ajax/gestion_consultores.php',
            type: 'GET',
            data: { action: 'listar_colaboradores', consultor_id: consultorId },
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'rol' },
            { data: 'fecha_asignacion' },
            { 
                data: null,
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-danger btn-eliminar-colaborador" data-id="${row.id}">Eliminar</button>`;
                }
            }
        ]
    });
}

function cargarClientesDisponibles() {
    $.ajax({
        url: 'ajax/gestion_consultores.php',
        type: 'GET',
        data: { action: 'listar_clientes_disponibles' },
        success: function(response) {
            const data = JSON.parse(response);
            const select = $('#selectClientesDisponibles');
            select.empty().append('<option value="">Seleccione un cliente</option>');
            
            data.forEach(cliente => {
                select.append(`<option value="${cliente.id}">${cliente.nombre} (${cliente.email})</option>`);
            });
        }
    });
}

function cargarConsultoresColaboradores(consultorPrincipalId) {
    $.ajax({
        url: 'ajax/gestion_consultores.php',
        type: 'GET',
        data: { action: 'listar_consultores_colaboradores', consultor_principal_id: consultorPrincipalId },
        success: function(response) {
            const data = JSON.parse(response);
            const select = $('#selectConsultoresColaboradores');
            select.empty().append('<option value="">Seleccione un consultor colaborador</option>');
            
            data.forEach(consultor => {
                select.append(`<option value="${consultor.id}">${consultor.nombre} (${consultor.rol})</option>`);
            });
        }
    });
}