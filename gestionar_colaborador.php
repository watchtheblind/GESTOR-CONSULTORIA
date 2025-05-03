<?php
session_start();
require_once 'database.php';
require_once 'auth_functions.php';

// Verificar permisos
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    header('Location: index.php');
    exit();
}

if (isset($_POST['consultor_principal_id']) && isset($_POST['consultor_colaborador_id']) && isset($_POST['accion_colaborador'])) {
    $consultor_principal_id = $_POST['consultor_principal_id'];
    $colaborador_id = $_POST['consultor_colaborador_id'];
    $accion = $_POST['accion_colaborador'];

    try {
        if ($accion === 'agregar') {
            $query = "INSERT INTO consultor_colaborador (consultor_principal_id, colaborador_id) 
                      VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$consultor_principal_id, $colaborador_id]);
            $_SESSION['success_message'] = "Colaborador agregado exitosamente.";
        } else {
            $query = "DELETE FROM consultor_colaborador 
                      WHERE consultor_principal_id = ? AND colaborador_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$consultor_principal_id, $colaborador_id]);
            $_SESSION['success_message'] = "Colaborador eliminado exitosamente.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error al " . ($accion === 'agregar' ? 'agregar' : 'eliminar') . " colaborador.";
    }
}

// Redirigir de vuelta a usuarios.php
header('Location: usuarios.php');
exit();
