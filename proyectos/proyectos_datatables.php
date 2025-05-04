<?php
session_start();
require_once '../database.php';

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
    'p.id',
    'p.nombre',
    'p.descripcion',
    'c.nombre_usuario',
    'p.estado',
    'p.observaciones'
];
$orderBy = $columns[$orderColumn] . ' ' . $orderDir;

// Consulta base con joins
$query = "SELECT SQL_CALC_FOUND_ROWS 
            p.id,
            p.nombre,
            p.descripcion,
            c.nombre_usuario as cliente,
            p.estado,
            p.observaciones
          FROM proyectos p
          LEFT JOIN usuarios c ON p.cliente_id = c.id";

// Filtros
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(p.nombre LIKE ? OR p.descripcion LIKE ? OR c.nombre_usuario LIKE ? OR p.observaciones LIKE ?)";
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
    "data" => $resultados
];

echo json_encode($response);
