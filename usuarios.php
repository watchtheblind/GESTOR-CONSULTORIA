<?php
session_start();
require_once 'auth_functions.php';

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
                <!-- Pestañas -->
                <ul class="nav nav-tabs" id="usuariosTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab" aria-controls="usuarios" aria-selected="true">Todos los Usuarios</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="consultores-tab" data-bs-toggle="tab" data-bs-target="#consultores" type="button" role="tab" aria-controls="consultores" aria-selected="false">Gestión de Consultores</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tercera-tab" data-bs-toggle="tab" data-bs-target="#tercera" type="button" role="tab" aria-controls="tercera" aria-selected="false">Tercera Pestaña</button>
                    </li>
                </ul>

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="usuariosTabsContent">
                    <!-- Pestaña de todos los usuarios -->
                    <div class="tab-pane fade show active" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
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

                    <!-- Pestaña de gestión de consultores -->
                    <div class="tab-pane fade" id="consultores" role="tabpanel" aria-labelledby="consultores-tab">
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Lista de Consultores</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <select id="selectConsultores" class="form-select">
                                                <option value="">Seleccione un consultor</option>
                                            </select>
                                        </div>
                                        <div id="consultorInfo" class="d-none">
                                            <h6>Información del Consultor</h6>
                                            <p><strong>Nombre:</strong> <span id="consultorNombre"></span></p>
                                            <p><strong>Correo:</strong> <span id="consultorEmail"></span></p>
                                            <p><strong>Rol:</strong> <span id="consultorRol"></span></p>

                                            <div class="mt-3">
                                                <button id="btnAsignarCliente" class="btn btn-sm btn-primary mb-2">Asignar Cliente</button>
                                                <button id="btnRetirarCliente" class="btn btn-sm btn-danger mb-2">Retirar Cliente</button>
                                                <button id="btnEnviarMensaje" class="btn btn-sm btn-info mb-2">Enviar Mensaje</button>
                                                <button id="btnAsignarTarea" class="btn btn-sm btn-warning mb-2">Asignar Tarea</button>
                                                <button id="btnGestionarColaboradores" class="btn btn-sm btn-secondary">Gestionar Colaboradores</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Clientes Asignados</h5>
                                    </div>
                                    <div class="card-body">
                                        <table id="clientesAsignadosTable" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Correo</th>
                                                    <th>Fecha Asignación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5>Colaboradores</h5>
                                    </div>
                                    <div class="card-body">
                                        <table id="colaboradoresTable" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Rol</th>
                                                    <th>Fecha Asignación</th>
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

                    <!-- Tercera pestaña -->
                    <div class="tab-pane fade" id="tercera" role="tabpanel" aria-labelledby="tercera-tab">
                        <div class="alert alert-info mt-3">
                            Esta pestaña será implementada posteriormente.
                        </div>
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
            <form id="asignarClienteForm">
                <div class="modal-body">
                    <input type="hidden" id="consultorId" name="consultor_id">
                    <div class="mb-3">
                        <label for="selectClientesDisponibles" class="form-label">Clientes Disponibles</label>
                        <select id="selectClientesDisponibles" class="form-select" name="cliente_id" required>
                            <option value="">Seleccione un cliente</option>
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
            <form id="colaboradoresForm">
                <div class="modal-body">
                    <input type="hidden" id="consultorPrincipalId" name="consultor_principal_id">
                    <div class="mb-3">
                        <label for="selectConsultoresColaboradores" class="form-label">Consultores Disponibles</label>
                        <select id="selectConsultoresColaboradores" class="form-select" name="consultor_colaborador_id">
                            <option value="">Seleccione un consultor colaborador</option>
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
            <form id="mensajeForm">
                <div class="modal-body">
                    <input type="hidden" id="mensajeConsultorId" name="consultor_id">
                    <div class="mb-3">
                        <label for="asuntoMensaje" class="form-label">Asunto</label>
                        <input type="text" class="form-control" id="asuntoMensaje" name="asunto" required>
                    </div>
                    <div class="mb-3">
                        <label for="contenidoMensaje" class="form-label">Mensaje</label>
                        <textarea class="form-control" id="contenidoMensaje" name="contenido" rows="5" required></textarea>
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

<!-- Modal para Asignar Tarea -->
<div class="modal fade" id="tareaModal" tabindex="-1" aria-labelledby="tareaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tareaModalLabel">Asignar Tarea al Consultor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tareaForm">
                <div class="modal-body">
                    <input type="hidden" id="tareaConsultorId" name="consultor_id">
                    <div class="mb-3">
                        <label for="tituloTarea" class="form-label">Título</label>
                        <input type="text" class="form-control" id="tituloTarea" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcionTarea" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionTarea" name="descripcion" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaVencimiento" class="form-label">Fecha de Vencimiento</label>
                        <input type="date" class="form-control" id="fechaVencimiento" name="fecha_vencimiento" required>
                    </div>
                    <div class="mb-3">
                        <label for="prioridadTarea" class="form-label">Prioridad</label>
                        <select class="form-select" id="prioridadTarea" name="prioridad" required>
                            <option value="baja">Baja</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<!-- Incluir Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Incluir Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<!-- Incluir DataTables con Bootstrap 5 -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.13.4/b-2.3.6/b-html5-2.3.6/datatables.min.css" />

<!-- Incluir jQuery y Bootstrap JS Bundle con Popper -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Incluir DataTables y extensiones -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.13.4/b-2.3.6/b-html5-2.3.6/datatables.min.js"></script>

<!-- Incluir nuestro script personalizado -->
<script src="js/usuarios.js"></script>