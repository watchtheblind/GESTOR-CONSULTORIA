<?php
session_start();
require_once 'database.php';
require_once 'auth_functions.php';

// Verificar permisos
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $estado = $_POST['estado'] ?? 'Activo';
    $creado_por = $_SESSION['id'];

    if ($cliente_id && $nombre) {
        try {
            $query = "INSERT INTO proyectos (nombre, descripcion, cliente_id, creado_por, estado) 
                     VALUES (?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            $stmt->execute([$nombre, $descripcion, $cliente_id, $creado_por, $estado]);

            $_SESSION['mensaje'] = "Proyecto creado correctamente";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al crear el proyecto: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Faltan datos requeridos";
    }
}

header('Location: usuarios.php');
exit();
