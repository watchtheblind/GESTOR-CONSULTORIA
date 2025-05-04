<?php
session_start();
require_once 'database.php';

// Verificar permisos
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Subadministrador') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            if (empty($_POST['usuario_id'])) {
                throw new Exception("El ID del usuario es requerido");
            }

            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$_POST['usuario_id']]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }

            echo json_encode($usuario);
            break;

        case 'create':
            if (empty($_POST['contrasena'])) {
                throw new Exception("La contraseña es requerida para nuevos usuarios");
            }

            // Verificar si el email ya existe
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo_electronico = ?");
            $stmt->execute([trim($_POST['correo_electronico'])]);
            if ($stmt->fetch()) {
                throw new Exception("Este email ya está en uso");
            }

            $data = [
                'nombre_usuario' => trim($_POST['nombre_usuario']),
                'correo_electronico' => trim($_POST['correo_electronico']),
                'rol' => $_POST['rol'],
                'numero_telefono' => $_POST['numero_telefono'] ?? null,
                'descripcion' => $_POST['descripcion'] ?? null,
                'esta_activo' => $_POST['esta_activo'] ?? 1
            ];

            $data['contrasena'] = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nombre_usuario, contrasena, correo_electronico, rol, numero_telefono, descripcion, esta_activo) 
                    VALUES (:nombre_usuario, :contrasena, :correo_electronico, :rol, :numero_telefono, :descripcion, :esta_activo)";

            $stmt = $conn->prepare($sql);
            $stmt->execute($data);

            // Obtener el ID del usuario creado
            $usuario_id = $conn->lastInsertId();

            // Crear carpeta para el usuario
            $directorio_usuario = "uploads/usuarios/$usuario_id";
            if (!file_exists($directorio_usuario)) {
                mkdir($directorio_usuario, 0777, true);
            }

            // Si el rol es Cliente, crear registro en tabla clientes
            if ($_POST['rol'] === 'Cliente') {
                $cliente_sql = "INSERT INTO clientes (nombre, correo_contacto) 
                              VALUES (?, ?)";
                $cliente_stmt = $conn->prepare($cliente_sql);
                $cliente_stmt->execute([
                    $data['nombre_usuario'],
                    $data['correo_electronico']
                ]);
            }

            echo json_encode(['success' => true]);
            break;

        case 'update':
            // Validar datos
            $required = ['nombre_usuario', 'correo_electronico', 'rol'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("El campo $field es requerido");
                }
            }

            // Verificar si el email ya existe en otro usuario
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo_electronico = ? AND id != ?");
            $stmt->execute([trim($_POST['correo_electronico']), $_POST['usuario_id']]);
            if ($stmt->fetch()) {
                throw new Exception("Este email ya está en uso");
            }

            $data = [
                'nombre_usuario' => trim($_POST['nombre_usuario']),
                'correo_electronico' => trim($_POST['correo_electronico']),
                'rol' => $_POST['rol'],
                'numero_telefono' => $_POST['numero_telefono'] ?? null,
                'descripcion' => $_POST['descripcion'] ?? null,
                'esta_activo' => $_POST['esta_activo'] ?? 1
            ];

            if (!empty($_POST['contrasena'])) {
                $data['contrasena'] = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
            }

            $sql = "UPDATE usuarios SET 
                    nombre_usuario = :nombre_usuario,
                    correo_electronico = :correo_electronico,
                    rol = :rol,
                    numero_telefono = :numero_telefono,
                    descripcion = :descripcion,
                    esta_activo = :esta_activo";

            if (!empty($_POST['contrasena'])) {
                $sql .= ", contrasena = :contrasena";
            }

            $sql .= " WHERE id = :id";
            $data['id'] = $_POST['usuario_id'];

            $stmt = $conn->prepare($sql);
            $stmt->execute($data);

            // Si el rol es Cliente, verificar si existe en tabla clientes
            if ($_POST['rol'] === 'Cliente') {
                // Verificar si ya existe en tabla clientes
                $check_cliente = "SELECT id FROM clientes WHERE correo_contacto = ?";
                $check_stmt = $conn->prepare($check_cliente);
                $check_stmt->execute([$data['correo_electronico']]);
                $cliente_existente = $check_stmt->fetch(PDO::FETCH_ASSOC);

                if (!$cliente_existente) {
                    // Si no existe, crear registro en tabla clientes
                    $cliente_sql = "INSERT INTO clientes (nombre, correo_contacto) 
                                  VALUES (?, ?)";
                    $cliente_stmt = $conn->prepare($cliente_sql);
                    $cliente_stmt->execute([
                        $data['nombre_usuario'],
                        $data['correo_electronico']
                    ]);
                }
            }

            echo json_encode(['success' => true]);
            break;

        case 'delete':
            if ($_SESSION['rol'] !== 'Administrador') {
                http_response_code(403);
                echo json_encode(['error' => "Solo el administrador puede eliminar usuarios"]);
                exit();
            }

            try {
                $usuario_id = $_POST['usuario_id'];

                // Eliminar carpeta del usuario
                $directorio_usuario = "uploads/usuarios/$usuario_id";
                if (file_exists($directorio_usuario)) {
                    // Eliminar todos los archivos y subdirectorios
                    $files = glob($directorio_usuario . '/*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    rmdir($directorio_usuario);
                }

                // Eliminar usuario de la base de datos
                $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$usuario_id]);

                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error al eliminar el usuario: ' . $e->getMessage()]);
            }
            break;

        case 'recover':
            if (isset($_POST['usuario_id']) && isset($_POST['nueva_contrasena'])) {
                $hashedPassword = password_hash($_POST['nueva_contrasena'], PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $_POST['usuario_id']]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Contraseña actualizada correctamente.'
                ]);
            } else {
                echo json_encode([
                    'error' => 'Datos incompletos.'
                ]);
            }
            break;

        default:
            throw new Exception("Acción no válida");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
