<?php
session_start();
require_once '../database.php';

// Desactivar la visualización de errores
error_reporting(0);
ini_set('display_errors', 0);

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Verificar permisos
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

// Configuración de límites
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB en bytes

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'subir':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $usuario_id = $_POST['usuario_id'] ?? null;
                $proyecto_id = $_POST['proyecto_id'] ?? null;

                if (!$usuario_id) {
                    throw new Exception('ID de usuario no proporcionado');
                }

                if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Error al subir el archivo: ' . getUploadErrorMessage($_FILES['archivo']['error']));
                }

                $archivo = $_FILES['archivo'];

                // Verificar tamaño del archivo
                if ($archivo['size'] > MAX_FILE_SIZE) {
                    throw new Exception('El archivo excede el tamaño máximo permitido de 10MB');
                }

                // Verificar si el archivo ya existe para este usuario
                $nombre_archivo = basename($archivo['name']);
                $ruta_completa = "../uploads/usuarios/$usuario_id/" . $nombre_archivo;

                // Verificar en la base de datos si ya existe un archivo con el mismo nombre para este usuario
                $stmt = $conn->prepare("SELECT COUNT(*) FROM archivos WHERE pertenece_a = ? AND ruta_archivo LIKE ?");
                $stmt->execute([$usuario_id, "%$nombre_archivo"]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception('Ya existe un archivo con el mismo nombre para este usuario');
                }

                $directorio_usuario = "../uploads/usuarios/$usuario_id";

                // Crear directorio si no existe
                if (!file_exists($directorio_usuario)) {
                    if (!mkdir($directorio_usuario, 0777, true)) {
                        throw new Exception('No se pudo crear el directorio para el usuario');
                    }
                }

                if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                    throw new Exception('Error al mover el archivo al directorio de destino');
                }

                // Guardar en la base de datos
                $stmt = $conn->prepare("INSERT INTO archivos (pertenece_a, proyecto_id, subido_por, ruta_archivo) VALUES (?, ?, ?, ?)");
                if (!$stmt->execute([$usuario_id, $proyecto_id, $_SESSION['id'], $ruta_completa])) {
                    // Si falla la inserción en la BD, eliminar el archivo subido
                    unlink($ruta_completa);
                    throw new Exception('Error al guardar el registro en la base de datos');
                }

                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        break;

    case 'eliminar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $archivo_id = $_POST['archivo_id'] ?? null;

            if (!$archivo_id) {
                echo json_encode(['error' => 'ID de archivo no proporcionado']);
                exit();
            }

            // Verificar que el archivo pertenece al usuario correcto
            $stmt = $conn->prepare("SELECT ruta_archivo, pertenece_a FROM archivos WHERE id = ?");
            $stmt->execute([$archivo_id]);
            $archivo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$archivo) {
                echo json_encode(['error' => 'Archivo no encontrado']);
                exit();
            }

            if ($archivo['pertenece_a'] != $_POST['usuario_id']) {
                echo json_encode(['error' => 'No tienes permiso para eliminar este archivo']);
                exit();
            }

            if (file_exists($archivo['ruta_archivo'])) {
                unlink($archivo['ruta_archivo']);
            }

            // Eliminar de la base de datos
            $stmt = $conn->prepare("DELETE FROM archivos WHERE id = ?");
            $stmt->execute([$archivo_id]);

            echo json_encode(['success' => true]);
        }
        break;

    case 'descargar':
        $archivo_id = $_GET['archivo_id'] ?? null;
        $usuario_id = $_GET['usuario_id'] ?? null;

        if (!$archivo_id || !$usuario_id) {
            echo json_encode(['error' => 'ID de archivo o usuario no proporcionado']);
            exit();
        }

        // Verificar que el archivo pertenece al usuario correcto
        $stmt = $conn->prepare("SELECT ruta_archivo FROM archivos WHERE id = ? AND pertenece_a = ?");
        $stmt->execute([$archivo_id, $usuario_id]);
        $archivo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$archivo || !file_exists($archivo['ruta_archivo'])) {
            echo json_encode(['error' => 'Archivo no encontrado']);
            exit();
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($archivo['ruta_archivo']) . '"');
        readfile($archivo['ruta_archivo']);
        exit();

    case 'listar':
        try {
            $usuario_id = $_GET['usuario_id'] ?? null;
            $proyecto_id = $_GET['proyecto_id'] ?? null;

            if (!$usuario_id) {
                throw new Exception('ID de usuario no proporcionado');
            }

            // Consulta para obtener archivos del usuario
            $sql = "SELECT a.*, u.nombre_usuario as subido_por_nombre, p.nombre as proyecto_nombre
                   FROM archivos a 
                   LEFT JOIN usuarios u ON a.subido_por = u.id 
                   LEFT JOIN proyectos p ON a.proyecto_id = p.id
                   WHERE a.pertenece_a = ?";

            $params = [$usuario_id];

            if ($proyecto_id && $proyecto_id !== '') {
                $sql .= " AND a.proyecto_id = ?";
                $params[] = $proyecto_id;
            }

            $sql .= " ORDER BY a.subido_en DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $archivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($archivos);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}

function getUploadErrorMessage($error_code)
{
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'El archivo excede el tamaño máximo permitido por el servidor';
        case UPLOAD_ERR_FORM_SIZE:
            return 'El archivo excede el tamaño máximo permitido por el formulario';
        case UPLOAD_ERR_PARTIAL:
            return 'El archivo fue subido parcialmente';
        case UPLOAD_ERR_NO_FILE:
            return 'No se seleccionó ningún archivo';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'No existe el directorio temporal';
        case UPLOAD_ERR_CANT_WRITE:
            return 'No se pudo escribir el archivo en el disco';
        case UPLOAD_ERR_EXTENSION:
            return 'La subida del archivo fue detenida por una extensión PHP';
        default:
            return 'Error desconocido al subir el archivo';
    }
}
