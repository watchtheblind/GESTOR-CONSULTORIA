<?php
session_start();
require_once 'auth_functions.php';
require_once 'database.php';

// Verificar permisos (todos los roles excepto Consultor Colaborador)
if ($_SESSION['rol'] === 'Consultor Colaborador') {
    header('Location: index.php');
    exit();
}

$titulo = "Gestión de Tareas";
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
                        <i class="fas fa-tasks me-1"></i>
                        Listado de Tareas
                    </div>
                    <?php if ($_SESSION['rol'] === 'Administrador' || $_SESSION['rol'] === 'Subadministrador' || $_SESSION['rol'] === 'Consultor Principal'): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearTareaModal">
                            <i class="fas fa-plus"></i> Nueva Tarea
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">Todas</button>
                        <button type="button" class="btn btn-outline-primary" data-filter="pending">Pendientes</button>
                        <button type="button" class="btn btn-outline-primary" data-filter="completed">Completadas</button>
                    </div>
                </div>
                <table id="tareasTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID Proyecto</th>
                            <th>Proyecto</th>
                            <th>Asignado a</th>
                            <th>Rol</th>
                            <th>Descripción</th>
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
</main>

<!-- Modal para Editar Tarea -->
<div class="modal fade" id="editarTareaModal" tabindex="-1" aria-labelledby="editarTareaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarTareaModalLabel">Editar Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editarTareaForm">
                <div class="modal-body">
                    <input type="hidden" id="tarea_id" name="id">
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="esta_completada" name="esta_completada">
                            <label class="form-check-label" for="esta_completada">
                                Tarea Completada
                            </label>
                        </div>
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

<!-- Modal para Crear Tarea -->
<div class="modal fade" id="crearTareaModal" tabindex="-1" aria-labelledby="crearTareaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearTareaModalLabel">Nueva Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="crearTareaForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="proyecto_id" class="form-label">Proyecto</label>
                        <select class="form-select" id="proyecto_id" name="proyecto_id" required>
                            <option value="">Seleccione un proyecto</option>
                            <?php
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
                                echo "<option value='{$proyecto['id']}'>{$proyecto['nombre']} - {$proyecto['nombre_cliente']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="asignado_a" class="form-label">Asignar a</label>
                        <select class="form-select" id="asignado_a" name="asignado_a" required>
                            <option value="">Seleccione un usuario</option>
                            <?php
                            $query = "SELECT id, nombre_usuario, rol FROM usuarios 
                                    WHERE rol IN ('Cliente', 'Consultor Principal') 
                                    ORDER BY nombre_usuario ASC";
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($usuarios as $usuario) {
                                echo "<option value='{$usuario['id']}'>{$usuario['nombre_usuario']} ({$usuario['rol']})</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="js/tareas.js"></script>

<?php include 'footer.php'; ?>