<?php
session_start();
require_once '../database.php';

// Verificar permisos
if ($_SESSION['rol'] !== 'Administrador') {
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

    // Eliminar directorio del usuario y su contenido
    $directorio_usuario = "../uploads/usuarios/$usuario_id";
    if (file_exists($directorio_usuario)) {
        // Eliminar todos los archivos y subdirectorios
        $files = glob($directorio_usuario . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($directorio_usuario);
    }

    echo json_encode(['success' => true]);
}
