<?php
session_start();
require_once 'database.php';

// Solo permitir acceso a admin y subadmin
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consultor_id = $_POST['consultor_id'] ?? null;
    $cliente_id = $_POST['cliente_id'] ?? null;

    if (!$consultor_id || !$cliente_id) {
        $_SESSION['error'] = "Datos incompletos";
        header('Location: usuarios.php');
        exit();
    }

    try {
        // Insertar la relación en la tabla cliente_consultor
        $query = "INSERT INTO cliente_consultor (cliente_id, consultor_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$cliente_id, $consultor_id]);

        $_SESSION['success'] = "Cliente asignado correctamente al consultor";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Código de error para duplicados
            $_SESSION['error'] = "Este consultor ya está asignado a este cliente";
        } else {
            $_SESSION['error'] = "Error al asignar el cliente: " . $e->getMessage();
        }
    }

    header('Location: usuarios.php');
    exit();
}
