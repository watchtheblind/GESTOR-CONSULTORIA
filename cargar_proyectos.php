<?php
session_start();
require_once 'database.php';
require_once 'auth_functions.php';

if (!isset($_SESSION['id']) || !isset($_GET['cliente_id'])) {
    http_response_code(403);
    exit();
}

$cliente_id = $_GET['cliente_id'];

try {
    $query = "SELECT id, nombre, descripcion, estado 
              FROM proyectos 
              WHERE cliente_id = ? 
              ORDER BY creado_en DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute([$cliente_id]);
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($proyectos as $proyecto) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($proyecto['nombre']) . '</td>';
        echo '<td>' . htmlspecialchars($proyecto['descripcion']) . '</td>';
        echo '<td>' . htmlspecialchars($proyecto['estado']) . '</td>';
        echo '<td>';
        if ($proyecto['estado'] === 'Activo') {
            echo '<form action="cerrar_proyecto.php" method="POST" style="display:inline;">';
            echo '<input type="hidden" name="proyecto_id" value="' . $proyecto['id'] . '">';
            echo '<button type="submit" class="btn btn-sm btn-danger">Cerrar Proyecto</button>';
            echo '</form>';
        }
        echo '</td>';
        echo '</tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="4">Error al cargar los proyectos</td></tr>';
}
