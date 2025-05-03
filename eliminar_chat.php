<?php
session_start();
require_once 'database.php';
require_once 'auth_functions.php';

header('Content-Type: application/json');

// Verificar permisos
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cliente_id'])) {
    $usuario_id = $_SESSION['id'];
    $cliente_id = $_POST['cliente_id'];

    try {
        // Eliminar todos los mensajes donde el usuario actual y el cliente son remitente/receptor
        $query = "DELETE FROM mensajes_chat 
                  WHERE (remitente_id = ? AND receptor_id = ?) 
                  OR (remitente_id = ? AND receptor_id = ?)";

        $stmt = $conn->prepare($query);
        $stmt->execute([$usuario_id, $cliente_id, $cliente_id, $usuario_id]);

        // Verificar si se eliminaron mensajes
        $filas_afectadas = $stmt->rowCount();

        if ($filas_afectadas > 0) {
            echo json_encode(['success' => true, 'message' => 'Chat eliminado correctamente']);
        } else {
            echo json_encode(['success' => true, 'message' => 'No había mensajes para eliminar']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar el chat: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos']);
}
