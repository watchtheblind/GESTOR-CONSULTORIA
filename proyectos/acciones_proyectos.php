<?php
session_start();
require_once '../database.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_observaciones':
        $proyecto_id = $_POST['proyecto_id'] ?? null;
        $observaciones = $_POST['observaciones'] ?? '';

        if (!$proyecto_id) {
            echo json_encode(['success' => false, 'error' => 'ID de proyecto no proporcionado']);
            exit;
        }

        try {
            $stmt = $conn->prepare("UPDATE proyectos SET observaciones = ? WHERE id = ?");
            $stmt->execute([$observaciones, $proyecto_id]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar las observaciones']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        break;
}
