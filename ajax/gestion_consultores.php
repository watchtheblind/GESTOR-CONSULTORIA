<?php
session_start();
require_once '../auth_functions.php';
require_once '../database.php';

// Verificar permisos y autenticación
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.0 401 Unauthorized');
    die('No autorizado');
}

if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    header('HTTP/1.0 403 Forbidden');
    die('Acceso prohibido');
}

$action = $_REQUEST['action'] ?? '';
$response = ['success' => false, 'message' => 'Acción no válida'];

try {
    switch ($action) {
        case 'listar_consultores':
            $stmt = $pdo->prepare("SELECT id, nombre_usuario as nombre, rol FROM usuarios WHERE rol IN ('Consultor Principal', 'Consultor Colaborador')");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'obtener_consultor':
            $id = $_GET['id'];
            $stmt = $pdo->prepare("SELECT id, nombre_usuario as nombre, correo_electronico as email, rol FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $response = $stmt->fetch(PDO::FETCH_ASSOC);
            break;

        case 'listar_clientes_asignados':
            $consultorId = $_GET['consultor_id'];
            $stmt = $pdo->prepare("
                SELECT c.id, c.nombre_usuario as nombre, c.correo_electronico as email, ac.fecha_asignacion 
                FROM asignacion_consultores ac
                JOIN usuarios c ON ac.cliente_id = c.id
                WHERE ac.consultor_id = ?
            ");
            $stmt->execute([$consultorId]);
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'listar_colaboradores':
            $consultorId = $_GET['consultor_id'];
            $stmt = $pdo->prepare("
                SELECT c.id, c.nombre_usuario as nombre, c.rol, cc.fecha_asignacion 
                FROM consultores_colaboradores cc
                JOIN usuarios c ON cc.consultor_colaborador_id = c.id
                WHERE cc.consultor_principal_id = ?
            ");
            $stmt->execute([$consultorId]);
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'listar_clientes_disponibles':
            $stmt = $pdo->prepare("
                SELECT id, nombre_usuario as nombre, correo_electronico as email 
                FROM usuarios 
                WHERE rol = 'Cliente' 
                AND id NOT IN (SELECT cliente_id FROM asignacion_consultores)
            ");
            $stmt->execute();
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'listar_consultores_colaboradores':
            $consultorPrincipalId = $_GET['consultor_principal_id'];
            $stmt = $pdo->prepare("
                SELECT id, nombre_usuario as nombre, rol 
                FROM usuarios 
                WHERE rol IN ('Consultor Principal', 'Consultor Colaborador') 
                AND id != ?
                AND id NOT IN (
                    SELECT consultor_colaborador_id 
                    FROM consultores_colaboradores 
                    WHERE consultor_principal_id = ?
                )
            ");
            $stmt->execute([$consultorPrincipalId, $consultorPrincipalId]);
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'asignar_cliente':
            $consultorId = $_POST['consultor_id'];
            $clienteId = $_POST['cliente_id'];

            // Verificar si ya está asignado
            $stmt = $pdo->prepare("SELECT id FROM asignacion_consultores WHERE consultor_id = ? AND cliente_id = ?");
            $stmt->execute([$consultorId, $clienteId]);

            if ($stmt->fetch()) {
                $response = ['success' => false, 'message' => 'Este cliente ya está asignado a este consultor'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO asignacion_consultores (consultor_id, cliente_id, fecha_asignacion) VALUES (?, ?, NOW())");
                if ($stmt->execute([$consultorId, $clienteId])) {
                    $response = ['success' => true, 'message' => 'Cliente asignado correctamente'];
                } else {
                    $response = ['success' => false, 'message' => 'Error al asignar cliente'];
                }
            }
            break;

        case 'gestionar_colaborador':
            $consultorPrincipalId = $_POST['consultor_principal_id'];
            $consultorColaboradorId = $_POST['consultor_colaborador_id'];
            $accion = $_POST['accion_colaborador'];

            if ($accion === 'agregar') {
                // Verificar si ya es colaborador
                $stmt = $pdo->prepare("SELECT id FROM consultores_colaboradores WHERE consultor_principal_id = ? AND consultor_colaborador_id = ?");
                $stmt->execute([$consultorPrincipalId, $consultorColaboradorId]);

                if ($stmt->fetch()) {
                    $response = ['success' => false, 'message' => 'Este consultor ya es colaborador'];
                } else {
                    $stmt = $pdo->prepare("INSERT INTO consultores_colaboradores (consultor_principal_id, consultor_colaborador_id, fecha_asignacion) VALUES (?, ?, NOW())");
                    if ($stmt->execute([$consultorPrincipalId, $consultorColaboradorId])) {
                        $response = ['success' => true, 'message' => 'Colaborador agregado correctamente'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error al agregar colaborador'];
                    }
                }
            } else {
                // Eliminar colaborador
                $stmt = $pdo->prepare("DELETE FROM consultores_colaboradores WHERE consultor_principal_id = ? AND consultor_colaborador_id = ?");
                if ($stmt->execute([$consultorPrincipalId, $consultorColaboradorId])) {
                    $response = ['success' => true, 'message' => 'Colaborador eliminado correctamente'];
                } else {
                    $response = ['success' => false, 'message' => 'Error al eliminar colaborador'];
                }
            }
            break;

        case 'enviar_mensaje':
            $consultorId = $_POST['consultor_id'];
            $asunto = $_POST['asunto'];
            $contenido = $_POST['contenido'];
            $remitenteId = $_SESSION['user_id'];

            $stmt = $pdo->prepare("INSERT INTO mensajes (remitente_id, destinatario_id, asunto, contenido, fecha_envio) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt->execute([$remitenteId, $consultorId, $asunto, $contenido])) {
                $response = ['success' => true, 'message' => 'Mensaje enviado correctamente'];
            } else {
                $response = ['success' => false, 'message' => 'Error al enviar mensaje'];
            }
            break;

        case 'asignar_tarea':
            $consultorId = $_POST['consultor_id'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $fechaVencimiento = $_POST['fecha_vencimiento'];
            $prioridad = $_POST['prioridad'];
            $creadorId = $_SESSION['user_id'];

            $stmt = $pdo->prepare("INSERT INTO tareas (titulo, descripcion, fecha_creacion, fecha_vencimiento, prioridad, creador_id, asignado_a, estado) VALUES (?, ?, NOW(), ?, ?, ?, ?, 'pendiente')");
            if ($stmt->execute([$titulo, $descripcion, $fechaVencimiento, $prioridad, $creadorId, $consultorId])) {
                $response = ['success' => true, 'message' => 'Tarea asignada correctamente'];
            } else {
                $response = ['success' => false, 'message' => 'Error al asignar tarea'];
            }
            break;

        default:
            $response = ['success' => false, 'message' => 'Acción no reconocida'];
    }
} catch (PDOException $e) {
    $response = ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
