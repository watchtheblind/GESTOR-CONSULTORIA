<?php
session_start();
require_once '../database.php';

// Verificar permisos
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? null;

    if (!$usuario_id) {
        echo json_encode(['error' => 'ID de usuario no proporcionado']);
        exit();
    }

    // Crear directorio para el usuario
    $directorio_usuario = "../uploads/usuarios/$usuario_id";
    if (!file_exists($directorio_usuario)) {
        mkdir($directorio_usuario, 0777, true);
    }

    echo json_encode(['success' => true]);
}
