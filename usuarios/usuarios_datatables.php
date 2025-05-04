<?php
session_start();
require_once 'database.php';

// Solo permitir acceso a admin y subadmin
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    http_response_code(403);
    exit();
}

header('Content-Type: application/json');

// Parámetros de DataTables
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$search = $_POST['search']['value'] ?? '';
$draw = $_POST['draw'] ?? 1;

// Columnas a ordenar
$orderColumn = $_POST['order'][0]['column'] ?? 0;
$orderDir = $_POST['order'][0]['dir'] ?? 'asc';
$columns = [
    'id',
    'nombre_usuario',
    'correo_electronico',
    'rol',
    'esta_activo',
    'creado_en'
];
$orderBy = $columns[$orderColumn] . ' ' . $orderDir;

// Consulta base
$query = "SELECT SQL_CALC_FOUND_ROWS 
            id, nombre_usuario, correo_electronico, rol, 
            CASE esta_activo WHEN 1 THEN 'Activo' ELSE 'Inactivo' END as estado,
            DATE_FORMAT(creado_en, '%d/%m/%Y %H:%i') as creado_en
          FROM usuarios";

// Filtros
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(nombre_usuario LIKE ? OR correo_electronico LIKE ? OR rol LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

// Ordenación y paginación
$query .= " ORDER BY $orderBy LIMIT $start, $length";

// Ejecutar consulta
$stmt = $conn->prepare($query);
$stmt->execute($params);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total de registros
$total = $conn->query("SELECT FOUND_ROWS()")->fetchColumn();

// Formatear respuesta
$response = [
    "draw" => intval($draw),
    "recordsTotal" => $total,
    "recordsFiltered" => $total,
    "data" => []
];

foreach ($resultados as $row) {
    $acciones = '
        <div class="btn-group" role="group">
            <button class="btn btn-sm btn-warning editarUsuario" data-id="' . $row['id'] . '" title="Editar">
                <i class="bi bi-pencil"></i>
            </button>';

    if ($_SESSION['rol'] === 'Administrador') {
        $acciones .= '
            <button class="btn btn-sm btn-danger eliminarUsuario" data-id="' . $row['id'] . '" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>';
    }

    $acciones .= '
            <button class="btn btn-sm btn-info recuperarContrasena" data-id="' . $row['id'] . '" data-nombre="' . $row['nombre_usuario'] . '" title="Recuperar Contraseña">
                <i class="bi bi-key"></i>
            </button>
        </div>';

    $response['data'][] = [
        $row['id'],
        $row['nombre_usuario'],
        $row['correo_electronico'],
        $row['rol'],
        $row['estado'],
        $row['creado_en'],
        $acciones
    ];
}

echo json_encode($response);
