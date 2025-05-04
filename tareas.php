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
                </div>
            </div>
            <div class="card-body">
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

<script src="js/tareas.js"></script>

<?php include 'footer.php'; ?>