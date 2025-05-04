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

if (!$id && $action !== 'create') {
    echo json_encode(['error' => 'ID de tarea no proporcionado']);
    exit();
}

switch ($action) {
    case 'create':
        // Verificar permisos para crear tareas
        if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador' && $_SESSION['rol'] !== 'Consultor Principal') {
            echo json_encode(['success' => false, 'error' => 'No tienes permisos para crear tareas']);
            exit();
        }

        $proyecto_id = $_POST['proyecto_id'] ?? null;
        $asignado_a = $_POST['asignado_a'] ?? null;
        $descripcion = $_POST['descripcion'] ?? '';

        if (!$proyecto_id || !$asignado_a || empty($descripcion)) {
            echo json_encode(['success' => false, 'error' => 'Todos los campos son requeridos']);
            exit();
        }

        try {
            $query = "INSERT INTO tareas (proyecto_id, asignado_a, descripcion, esta_completada) 
                     VALUES (?, ?, ?, 0)";

            $stmt = $conn->prepare($query);
            $stmt->execute([$proyecto_id, $asignado_a, $descripcion]);

            echo json_encode(['success' => true, 'message' => 'Tarea creada correctamente']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Error al crear la tarea: ' . $e->getMessage()]);
        }
        break;

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
        // Verificar permisos para actualizar
        if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
            echo json_encode(['error' => 'No tienes permisos para actualizar tareas']);
            exit();
        }

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

    case 'complete':
        // Verificar permisos para marcar como completada
        if (
            $_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador' &&
            $_SESSION['rol'] !== 'Consultor Principal' && $_SESSION['rol'] !== 'Cliente'
        ) {
            echo json_encode(['error' => 'No tienes permisos para marcar tareas como completadas']);
            exit();
        }

        try {
            // Verificar si el usuario tiene permiso para marcar esta tarea
            $query = "SELECT asignado_a FROM tareas WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            $tarea = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tarea) {
                echo json_encode(['error' => 'Tarea no encontrada']);
                exit();
            }

            if ($_SESSION['rol'] === 'Cliente' && $tarea['asignado_a'] != $_SESSION['user_id']) {
                echo json_encode(['error' => 'No tienes permiso para marcar esta tarea como completada']);
                exit();
            }

            $query = "UPDATE tareas SET esta_completada = 1 WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Tarea marcada como completada']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error al marcar la tarea como completada: ' . $e->getMessage()]);
        }
        break;

    case 'delete':
        // Verificar permisos para eliminar
        if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
            echo json_encode(['error' => 'No tienes permisos para eliminar tareas']);
            exit();
        }

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
