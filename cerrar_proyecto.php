<?php
session_start();
require_once 'database.php';
require_once 'auth_functions.php';

if (!isset($_SESSION['id']) || !isset($_POST['proyecto_id'])) {
    header('Location: index.php');
    exit();
}

$proyecto_id = $_POST['proyecto_id'];

try {
    $query = "UPDATE proyectos SET estado = 'Cerrado' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$proyecto_id]);

    $_SESSION['mensaje'] = "Proyecto cerrado correctamente";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cerrar el proyecto: " . $e->getMessage();
}

header('Location: usuarios.php');
exit();
