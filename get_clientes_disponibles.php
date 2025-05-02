<?php
session_start();
require_once 'database.php';

// Solo permitir acceso a admin y subadmin
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    http_response_code(403);
    exit();
}

// Obtener el ID del consultor si se proporciona
$consultor_id = isset($_GET['consultor_id']) ? $_GET['consultor_id'] : null;

// Consulta para obtener los clientes disponibles
$query = "SELECT u.id, u.nombre_usuario, u.correo_electronico, 
                 c.consultor_asignado_id, u2.nombre_usuario as nombre_consultor
          FROM usuarios u
          LEFT JOIN clientes c ON u.id = c.usuario_id
          LEFT JOIN usuarios u2 ON c.consultor_asignado_id = u2.id
          WHERE u.rol = 'Cliente'
          AND (c.consultor_asignado_id IS NULL OR c.consultor_asignado_id = ?)";

$stmt = $conn->prepare($query);
$stmt->execute([$consultor_id]);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generar opciones del select
$options = '<option value="">Seleccione un cliente</option>';
foreach ($clientes as $cliente) {
    $selected = ($cliente['consultor_asignado_id'] == $consultor_id) ? 'selected' : '';
    $options .= sprintf(
        '<option value="%d" %s>%s - %s</option>',
        $cliente['id'],
        $selected,
        htmlspecialchars($cliente['nombre_usuario']),
        htmlspecialchars($cliente['correo_electronico'])
    );
}

echo $options;
