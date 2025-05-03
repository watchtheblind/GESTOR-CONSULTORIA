<?php
session_start();
require_once 'auth_functions.php';
require_once 'database.php';

// Verificar permisos (solo admin)
if ($_SESSION['rol'] !== 'Administrador') {
    header('Location: index.php');
    exit();
}

// Cargar configuraciones existentes
$configuraciones = [];
try {
    $stmt = $conn->query("SELECT clave, valor FROM configuraciones_sistema");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $configuraciones[$row['clave']] = $row['valor'];
    }
} catch (PDOException $e) {
    // Si la tabla no existe, no mostrar error
}

$titulo = "Configuración del Sistema";
?>
<main>
    <?php include 'header.php'; ?>
    <aside>
        <?php include 'sidebar.php'; ?>
    </aside>
    <div class="container-fluid px-4">
        <h1 class="mt-4"><?= $titulo ?></h1>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensaje'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-cog me-1"></i>
                Ajustes del Sistema
            </div>
            <div class="card-body">
                <form action="guardar_configuracion.php" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="correo_administrativo" class="form-label">Correo Electrónico Administrativo</label>
                            <input type="email" class="form-control" id="correo_administrativo" name="correo_administrativo"
                                value="<?= htmlspecialchars($configuraciones['correo_administrativo'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="marca_empresa" class="form-label">Marca de la Empresa</label>
                            <input type="text" class="form-control" id="marca_empresa" name="marca_empresa"
                                value="<?= htmlspecialchars($configuraciones['marca_empresa'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="telefono_contacto" class="form-label">Teléfono de Contacto</label>
                            <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto"
                                value="<?= htmlspecialchars($configuraciones['telefono_contacto'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="direccion_empresa" class="form-label">Dirección de la Empresa</label>
                            <input type="text" class="form-control" id="direccion_empresa" name="direccion_empresa"
                                value="<?= htmlspecialchars($configuraciones['direccion_empresa'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="logo_empresa" class="form-label">Logo de la Empresa</label>
                            <?php if (!empty($configuraciones['logo_empresa'])): ?>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($configuraciones['logo_empresa']) ?>" alt="Logo actual" style="max-height: 100px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="logo_empresa" name="logo_empresa" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label for="tiempo_sesion" class="form-label">Tiempo de Sesión (minutos)</label>
                            <input type="number" class="form-control" id="tiempo_sesion" name="tiempo_sesion"
                                value="<?= htmlspecialchars($configuraciones['tiempo_sesion'] ?? '30') ?>" min="5" max="120">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="descripcion_empresa" class="form-label">Descripción de la Empresa</label>
                            <textarea class="form-control" id="descripcion_empresa" name="descripcion_empresa" rows="3"><?= htmlspecialchars($configuraciones['descripcion_empresa'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>