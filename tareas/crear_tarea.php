<?php
session_start();
require_once '../database.php';

// Verificar permisos
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    $_SESSION['error'] = 'No tienes permisos para crear tareas';
    header('Location: ../usuarios.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proyecto_id = $_POST['proyecto_id'] ?? null;
    $asignado_a = $_POST['asignado_a'] ?? null;
    $descripcion = $_POST['descripcion'] ?? '';
    $esta_completada = isset($_POST['esta_completada']) ? 1 : 0;

    if (!$proyecto_id || !$asignado_a || empty($descripcion)) {
        $_SESSION['error'] = 'Todos los campos son requeridos';
        header('Location: ../usuarios.php');
        exit();
    }

    try {
        $query = "INSERT INTO tareas (proyecto_id, asignado_a, descripcion, esta_completada) 
                 VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->execute([$proyecto_id, $asignado_a, $descripcion, $esta_completada]);

        $_SESSION['success'] = 'Tarea creada correctamente';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error al crear la tarea: ' . $e->getMessage();
    }

    header('Location: ../usuarios.php');
    exit();
}
