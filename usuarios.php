<?php
session_start();
require_once 'auth_functions.php';
require_once 'database.php';
// Verificar permisos (solo admin y subadmin)
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    header('Location: index.php');
    exit();
}

$titulo = "Gestión de Usuarios";
?>
<main>
    <?php include 'header.php'; ?>
    <aside>
        <?php include 'sidebar.php'; ?>
    </aside>
    <div class="container-fluid px-4">
        <h1 class="mt-4"><?= $titulo ?></h1>

        <!-- Pestañas de Bootstrap -->
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab" aria-controls="usuarios" aria-selected="true">
                    <i class="fas fa-users me-1"></i> Usuarios
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="consultores-tab" data-bs-toggle="tab" data-bs-target="#consultores" type="button" role="tab" aria-controls="consultores" aria-selected="false">
                    <i class="fas fa-user-tie me-1"></i> Consultores
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="otros-tab" data-bs-toggle="tab" data-bs-target="#otros" type="button" role="tab" aria-controls="otros" aria-selected="false">
                    <i class="fas fa-ellipsis-h me-1"></i> Clientes
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Pestaña de Usuarios -->
            <div class="tab-pane fade show active" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-users me-1"></i>
                                Listado de Usuarios
                            </div>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#usuarioModal">
                                <i class="fas fa-plus"></i> Nuevo Usuario
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="usuariosTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Usuario</th>
                                    <th>Correo Electrónico</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Creado En</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pestaña de Consultores -->
            <div class="tab-pane fade" id="consultores" role="tabpanel" aria-labelledby="consultores-tab">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user-tie me-1"></i>
                                Listado de Consultores
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="consultoresTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Consultor</th>
                                    <th>Correo Electrónico</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Creado En</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pestaña de Otros -->
            <div class="tab-pane fade" id="otros" role="tabpanel" aria-labelledby="otros-tab">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-users me-1"></i>
                                Listado de Clientes
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="clientesTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Cliente</th>
                                    <th>Correo Electrónico</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                    <th>Creado En</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<!-- Modal para Crear/Editar Usuario -->
<div class="modal fade" id="usuarioModal" tabindex="-1" aria-labelledby="usuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usuarioModalLabel">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="usuarioForm" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="usuario_id" name="usuario_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                        </div>
                        <div class="col-md-6">
                            <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" required>
                        </div>
                        <div class="col-md-6" id="passwordField">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contrasena" name="contrasena">
                            <small class="text-muted">Dejar en blanco para no cambiar</small>
                        </div>
                        <div class="col-md-6">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccionar...</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Subadministrador">Subadministrador</option>
                                <option value="Consultor Principal">Consultor Principal</option>
                                <option value="Consultor Colaborador">Consultor Colaborador</option>
                                <option value="Cliente">Cliente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="numero_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="numero_telefono" name="numero_telefono">
                        </div>
                        <div class="col-md-6">
                            <label for="esta_activo" class="form-label">Estado</label>
                            <select class="form-select" id="esta_activo" name="esta_activo">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Usuario -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Datos del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editarUsuarioForm" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="usuario_id" name="usuario_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                        </div>
                        <div class="col-md-6">
                            <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" required>
                        </div>
                        <div class="col-md-6" id="passwordField">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contrasena" name="contrasena">
                            <small class="text-muted">Dejar en blanco para no cambiar</small>
                        </div>
                        <div class="col-md-6">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccionar...</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Subadministrador">Subadministrador</option>
                                <option value="Consultor Principal">Consultor Principal</option>
                                <option value="Consultor Colaborador">Consultor Colaborador</option>
                                <option value="Cliente">Cliente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="numero_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="numero_telefono" name="numero_telefono">
                        </div>
                        <div class="col-md-6">
                            <label for="esta_activo" class="form-label">Estado</label>
                            <select class="form-select" id="esta_activo" name="esta_activo">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Recuperar Contraseña -->
<div class="modal fade" id="recuperarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Recuperar Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="recuperarForm">
                <div class="modal-body">
                    <input type="hidden" id="recuperar_id" name="usuario_id">
                    <p>¿Deseas generar una nueva contraseña para <span id="usuarioNombre"></span>?</p>
                    <div class="mb-3">
                        <label for="nueva_contrasena" class="form-label">Nueva Contraseña</label>
                        <input type="text" class="form-control" id="nueva_contrasena" name="nueva_contrasena">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal para Asignar Cliente -->
<div class="modal fade" id="asignarClienteModal" tabindex="-1" aria-labelledby="asignarClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignarClienteModalLabel">Asignar Cliente a Consultor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="asignar_cliente.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="consultorId" name="consultor_id">
                    <div class="mb-3">
                        <label for="selectClientesDisponibles" class="form-label">Clientes Disponibles</label>
                        <select id="selectClientesDisponibles" class="form-select" name="cliente_id" required>
                            <?php
                            // Obtener el ID del consultor si está en la URL
                            $consultor_id = isset($_GET['consultor_id']) ? $_GET['consultor_id'] : null;

                            // Consulta para obtener los clientes disponibles
                            $query = "SELECT c.id, c.nombre, c.correo_contacto
                                     FROM clientes c
                                     WHERE NOT EXISTS (
                                         SELECT 1 
                                         FROM cliente_consultor cc 
                                         WHERE cc.cliente_id = c.id 
                                         AND cc.consultor_id = ?
                                     )";

                            $stmt = $conn->prepare($query);
                            $stmt->execute([$consultor_id]);
                            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            echo '<option value="">Seleccione un cliente</option>';
                            foreach ($clientes as $cliente) {
                                printf(
                                    '<option value="%d">%s - %s</option>',
                                    $cliente['id'],
                                    htmlspecialchars($cliente['nombre']),
                                    htmlspecialchars($cliente['correo_contacto'])
                                );
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Gestionar Colaboradores -->
<div class="modal fade" id="colaboradoresModal" tabindex="-1" aria-labelledby="colaboradoresModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="colaboradoresModalLabel">Gestionar Colaboradores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="colaboradoresForm" action="gestionar_colaborador.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="consultorPrincipalId" name="consultor_principal_id">
                    <div class="mb-3">
                        <label for="selectConsultoresColaboradores" class="form-label">Consultores Disponibles</label>
                        <select id="selectConsultoresColaboradores" class="form-select" name="consultor_colaborador_id">
                            <option value="">Seleccione un consultor colaborador</option>
                            <?php
                            // Consulta para obtener consultores disponibles
                            $query = "SELECT id, nombre_usuario, rol 
                                    FROM usuarios 
                                    WHERE rol IN ('Consultor Principal', 'Consultor Colaborador')
                                    AND esta_activo = 1 
                                    ORDER BY nombre_usuario";

                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $consultores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($consultores as $consultor) {
                                printf(
                                    '<option value="%d">%s (%s)</option>',
                                    $consultor['id'],
                                    htmlspecialchars($consultor['nombre_usuario']),
                                    htmlspecialchars($consultor['rol'])
                                );
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="accion_colaborador" id="agregarColaborador" value="agregar" checked>
                        <label class="form-check-label" for="agregarColaborador">
                            Agregar como colaborador
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="accion_colaborador" id="eliminarColaborador" value="eliminar">
                        <label class="form-check-label" for="eliminarColaborador">
                            Eliminar como colaborador
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Enviar Mensaje -->
<div class="modal fade" id="mensajeModal" tabindex="-1" aria-labelledby="mensajeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mensajeModalLabel">Enviar Mensaje al Consultor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="enviar_mensaje.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="receptor_id" id="mensajeConsultorId">
                    <div class="mb-3">
                        <label for="mensaje" class="form-label">Mensaje</label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Tareas -->
<div class="modal fade" id="tareaModal" tabindex="-1" aria-labelledby="tareaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tareaModalLabel">Nueva Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="crear_tarea.php">
                <div class="modal-body">
                    <input type="hidden" name="asignado_a" id="tareaConsultorId">
                    <div class="mb-3">
                        <label for="proyecto_id" class="form-label">Proyecto</label>
                        <select class="form-select" id="proyecto_id" name="proyecto_id" required>
                            <option value="">Seleccione un proyecto</option>
                            <?php
                            // Consulta para obtener los proyectos activos
                            $query = "SELECT p.id, p.nombre, p.descripcion, 
                                    u.nombre_usuario as nombre_cliente
                                    FROM proyectos p
                                    LEFT JOIN usuarios u ON p.cliente_id = u.id
                                    WHERE p.estado = 'Activo'
                                    ORDER BY p.nombre ASC";

                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($proyectos as $proyecto) {
                                printf(
                                    '<option value="%d">%s - Cliente: %s</option>',
                                    $proyecto['id'],
                                    htmlspecialchars($proyecto['nombre']),
                                    htmlspecialchars($proyecto['nombre_cliente'] ?? 'Sin asignar')
                                );
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción de la Tarea</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="esta_completada" name="esta_completada" value="1">
                            <label class="form-check-label" for="esta_completada">
                                Tarea Completada
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Retirar Cliente -->
<div class="modal fade" id="retirarClienteModal" tabindex="-1" aria-labelledby="retirarClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="retirarClienteModalLabel">Retirar Cliente del Consultor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="retirarClienteForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="retirarConsultorId" name="consultor_id">
                    <div class="mb-3">
                        <label for="selectClientesAsignados" class="form-label">Clientes Asignados</label>
                        <select id="selectClientesAsignados" class="form-select" name="cliente_id" required>
                            <option value="">Seleccione un cliente</option>
                            <?php
                            // Obtener el ID del consultor del formulario
                            $consultor_id = isset($_POST['consultor_id']) ? $_POST['consultor_id'] : null;

                            if ($consultor_id) {
                                // Consulta para obtener los IDs de cliente_consultor
                                $query = "SELECT cc.id, cc.cliente_id, cc.consultor_id, cc.fecha_asignacion
                                         FROM cliente_consultor cc
                                         WHERE cc.consultor_id = ?
                                         ORDER BY cc.fecha_asignacion DESC";

                                $stmt = $conn->prepare($query);
                                $stmt->execute([$consultor_id]);
                                $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($asignaciones as $asignacion) {
                                    printf(
                                        '<option value="%d">ID Asignación: %d - Cliente ID: %d - Fecha: %s</option>',
                                        $asignacion['id'],
                                        $asignacion['id'],
                                        $asignacion['cliente_id'],
                                        $asignacion['fecha_asignacion']
                                    );
                                }
                            } else {
                                echo '<option value="" disabled>No hay ID de consultor seleccionado</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Retirar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Opciones de Mensajes -->
<div class="modal fade" id="mensajesClienteModal" tabindex="-1" aria-labelledby="mensajesClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mensajesClienteModalLabel">Opciones de Mensajes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" id="irAlChat">
                        <i class="bi bi-chat-dots me-2"></i>Ir al Chat
                    </button>
                    <button type="button" class="btn btn-danger" id="eliminarChat">
                        <i class="bi bi-trash me-2"></i>Eliminar Chat
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Proyectos -->
<div class="modal fade" id="proyectosClienteModal" tabindex="-1" aria-labelledby="proyectosClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proyectosClienteModalLabel">Gestionar Proyectos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabla de Proyectos Existentes -->
                <div class="table-responsive mb-4" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-striped">
                        <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 1;">
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="proyectosTableBody">
                            <!-- Los proyectos se cargarán aquí -->
                        </tbody>
                    </table>
                </div>

                <!-- Formulario para Nuevo Proyecto -->
                <h6>Crear Nuevo Proyecto</h6>
                <form action="gestionar_proyecto.php" method="POST">
                    <input type="hidden" name="cliente_id" id="proyectoClienteId">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Proyecto</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Activo">Activo</option>
                            <option value="Cerrado">Cerrado</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Tareas de Cliente -->
<div class="modal fade" id="tareaClienteModal" tabindex="-1" aria-labelledby="tareaClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tareaClienteModalLabel">Asignar Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="crear_tarea.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="asignado_a" id="tareaClienteId" value="">
                    <div class="mb-3">
                        <label for="proyecto_id" class="form-label">Proyecto</label>
                        <select class="form-select" id="proyecto_id" name="proyecto_id" required>
                            <option value="">Seleccione un proyecto</option>
                            <?php
                            // Consulta para obtener los proyectos activos
                            $query = "SELECT p.id, p.nombre, p.descripcion, 
                                    u.nombre_usuario as nombre_cliente
                                    FROM proyectos p
                                    LEFT JOIN usuarios u ON p.cliente_id = u.id
                                    WHERE p.estado = 'Activo'
                                    ORDER BY p.nombre ASC";

                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($proyectos as $proyecto) {
                                printf(
                                    '<option value="%d">%s - Cliente: %s</option>',
                                    $proyecto['id'],
                                    htmlspecialchars($proyecto['nombre']),
                                    htmlspecialchars($proyecto['nombre_cliente'] ?? 'Sin asignar')
                                );
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción de la Tarea</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="esta_completada" name="esta_completada" value="1">
                            <label class="form-check-label" for="esta_completada">
                                Tarea Completada
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Archivos de Cliente -->
<div class="modal fade" id="archivosClienteModal" tabindex="-1" aria-labelledby="archivosClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archivosClienteModalLabel">Archivos del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="subirArchivoForm" enctype="multipart/form-data">
                    <input type="hidden" id="archivoClienteId" name="usuario_id">
                    <div class="mb-3">
                        <label for="proyecto_id" class="form-label">Proyecto (opcional)</label>
                        <select class="form-select" id="proyecto_id" name="proyecto_id">
                            <option value="">Seleccione un proyecto</option>
                            <?php
                            $stmt = $conn->query("SELECT id, nombre FROM proyectos WHERE estado = 'Activo'");
                            while ($proyecto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$proyecto['id']}'>{$proyecto['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="archivo" class="form-label">Archivo</label>
                        <input type="file" class="form-control" id="archivo" name="archivo" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Subir Archivo</button>
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="table table-striped" id="archivosTable">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Proyecto</th>
                                <th>Subido por</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Manejador para el botón de archivos
        $(document).on('click', '.archivosCliente', function() {
            var id = $(this).data('id');
            var nombre = $(this).closest('tr').find('td:eq(1)').text();

            $('#archivoClienteId').val(id);
            $('#archivosClienteModalLabel').text('Archivos de ' + nombre);

            // Cargar archivos inicialmente sin filtro de proyecto
            cargarArchivos(id);

            $('#archivosClienteModal').modal('show');
        });

        // Manejador para el cambio de proyecto
        $('#proyecto_id').change(function() {
            var usuarioId = $('#archivoClienteId').val();
            cargarArchivos(usuarioId, $(this).val());
        });

        // Función para cargar archivos
        function cargarArchivos(usuarioId, proyectoId = null) {
            var url = 'archivos_logica/gestionar_archivos.php?action=listar&usuario_id=' + usuarioId;
            if (proyectoId) {
                url += '&proyecto_id=' + proyectoId;
            }

            $.get(url, function(data) {
                try {
                    var archivos = typeof data === 'string' ? JSON.parse(data) : data;
                    var tbody = $('#archivosTable tbody');
                    tbody.empty();

                    if (Array.isArray(archivos)) {
                        archivos.forEach(function(archivo) {
                            var nombreArchivo = archivo.ruta_archivo.split('/').pop();
                            var fecha = new Date(archivo.subido_en).toLocaleString();

                            tbody.append(`
                                <tr>
                                    <td>${nombreArchivo}</td>
                                    <td>${archivo.proyecto_nombre || 'Sin proyecto'}</td>
                                    <td>${archivo.subido_por_nombre}</td>
                                    <td>${fecha}</td>
                                    <td>
                                        <a href="archivos_logica/gestionar_archivos.php?action=descargar&archivo_id=${archivo.id}&usuario_id=${usuarioId}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger eliminarArchivo" 
                                                data-id="${archivo.id}"
                                                data-usuario-id="${usuarioId}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr><td colspan="5" class="text-center">No hay archivos disponibles</td></tr>');
                    }
                } catch (e) {
                    console.error('Error al procesar los archivos:', e);
                    $('#archivosTable tbody').html('<tr><td colspan="5" class="text-center">Error al cargar los archivos</td></tr>');
                }
            }).fail(function() {
                $('#archivosTable tbody').html('<tr><td colspan="5" class="text-center">Error al cargar los archivos</td></tr>');
            });
        }

        // Manejador para subir archivos
        $('#subirArchivoForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'archivos_logica/gestionar_archivos.php?action=subir',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        var data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            alert('Archivo subido correctamente');
                            $('#subirArchivoForm')[0].reset();
                            // Recargar la lista de archivos con el proyecto actual
                            var usuarioId = $('#archivoClienteId').val();
                            var proyectoId = $('#proyecto_id').val();
                            cargarArchivos(usuarioId, proyectoId);
                        } else {
                            alert('Error al subir el archivo: ' + (data.error || 'Error desconocido'));
                        }
                    } catch (e) {
                        console.error('Error al procesar la respuesta:', e);
                        alert('Error al procesar la respuesta del servidor');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición:', status, error);
                    alert('Error al subir el archivo: ' + (error || 'Error desconocido'));
                }
            });
        });

        // Manejador para eliminar archivos
        $(document).on('click', '.eliminarArchivo', function() {
            if (confirm('¿Estás seguro de eliminar este archivo?')) {
                var archivoId = $(this).data('id');
                var usuarioId = $(this).data('usuario-id');

                $.post('archivos_logica/gestionar_archivos.php?action=eliminar', {
                    archivo_id: archivoId,
                    usuario_id: usuarioId
                }, function(response) {
                    if (response.success) {
                        alert('Archivo eliminado correctamente');
                        // Recargar la página
                        location.reload();
                    } else {
                        alert('Error al eliminar el archivo: ' + response.error);
                    }
                });
            }
        });
    });
</script>

<?php include 'footer.php'; ?>