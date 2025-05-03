<?php
session_start();
require_once 'database.php';
require_once 'auth_functions.php';

// Verificar permisos
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receptor_id']) && isset($_POST['mensaje'])) {
    $remitente_id = $_SESSION['id'];
    $receptor_id = $_POST['receptor_id'];
    $mensaje = $_POST['mensaje'];

    try {
        $query = "INSERT INTO mensajes_chat (remitente_id, receptor_id, mensaje) 
                  VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$remitente_id, $receptor_id, $mensaje]);

        // Redirigir al chat
        header("Location: chat.php?receptor_id=" . $receptor_id);
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error al enviar el mensaje.";
        header('Location: usuarios.php');
        exit();
    }
} else {
    header('Location: usuarios.php');
    exit();
}
