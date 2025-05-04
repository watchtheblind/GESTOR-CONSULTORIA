<?php
session_start();
require_once '../database.php';

// Verificar permisos
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    echo json_encode(['error' => 'No tienes permisos para realizar esta acción']);
    exit();
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'ID de tarea no proporcionado']);
    exit();
}

switch ($action) {
    case 'get':
        // Obtener datos de la tarea
        try {
            $query = "SELECT t.*, p.nombre as proyecto_nombre, u.nombre_usuario 
                     FROM tareas t
                     LEFT JOIN proyectos p ON t.proyecto_id = p.id
                     LEFT JOIN usuarios u ON t.asignado_a = u.id
                     WHERE t.id = ?";

            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            $tarea = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tarea) {
                echo json_encode($tarea);
            } else {
                echo json_encode(['error' => 'Tarea no encontrada']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error al obtener la tarea: ' . $e->getMessage()]);
        }
        break;

    case 'update':
        // Actualizar tarea
        $descripcion = $_POST['descripcion'] ?? '';
        $esta_completada = isset($_POST['esta_completada']) ? 1 : 0;

        if (empty($descripcion)) {
            echo json_encode(['error' => 'La descripción es requerida']);
            exit();
        }

        try {
            $query = "UPDATE tareas 
                     SET descripcion = ?, esta_completada = ?
                     WHERE id = ?";

            $stmt = $conn->prepare($query);
            $stmt->execute([$descripcion, $esta_completada, $id]);

            echo json_encode(['success' => true, 'message' => 'Tarea actualizada correctamente']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error al actualizar la tarea: ' . $e->getMessage()]);
        }
        break;

    case 'delete':
        // Eliminar tarea
        try {
            $query = "DELETE FROM tareas WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Tarea eliminada correctamente']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error al eliminar la tarea: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
