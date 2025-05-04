<?php
session_start();
require_once '../database.php';

// Solo permitir acceso a roles autorizados
if ($_SESSION['rol'] === 'Consultor Colaborador') {
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
    't.id',
    't.proyecto_id',
    'p.nombre',
    'u.nombre_usuario',
    'u.rol',
    't.descripcion',
    't.esta_completada',
    't.creado_en'
];
$orderBy = $columns[$orderColumn] . ' ' . $orderDir;

// Consulta base con joins
$query = "SELECT SQL_CALC_FOUND_ROWS 
            t.id,
            t.proyecto_id,
            p.nombre as proyecto,
            u.nombre_usuario as asignado_a,
            u.rol,
            t.descripcion,
            CASE t.esta_completada WHEN 1 THEN 'Completada' ELSE 'Pendiente' END as estado,
            DATE_FORMAT(t.creado_en, '%d/%m/%Y %H:%i') as creado_en
          FROM tareas t
          LEFT JOIN proyectos p ON t.proyecto_id = p.id
          LEFT JOIN usuarios u ON t.asignado_a = u.id";

// Filtros
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(p.nombre LIKE ? OR u.nombre_usuario LIKE ? OR u.rol LIKE ? OR t.descripcion LIKE ?)";
    $params[] = "%$search%";
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
    $acciones = '<div class="btn-group" role="group">';

    if ($_SESSION['rol'] === 'Administrador' || $_SESSION['rol'] === 'Subadministrador') {
        $acciones .= '
            <button class="btn btn-sm btn-warning" title="Editar">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-danger" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>';
    }

    $acciones .= '</div>';

    $response['data'][] = [
        $row['id'],
        $row['proyecto_id'],
        $row['proyecto'],
        $row['asignado_a'],
        $row['rol'],
        $row['descripcion'],
        $row['estado'],
        $row['creado_en'],
        $acciones
    ];
}

echo json_encode($response);
