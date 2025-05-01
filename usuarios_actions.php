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
        case 'create':
        case 'update':
            // Validar datos
            $required = ['nombre_usuario', 'correo_electronico', 'rol'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("El campo $field es requerido");
                }
            }

            $data = [
                'nombre_usuario' => trim($_POST['nombre_usuario']),
                'correo_electronico' => trim($_POST['correo_electronico']),
                'rol' => $_POST['rol'],
                'numero_telefono' => $_POST['numero_telefono'] ?? null,
                'descripcion' => $_POST['descripcion'] ?? null,
                'esta_activo' => $_POST['esta_activo'] ?? 1
            ];

            if ($action === 'create') {
                if (empty($_POST['contrasena'])) {
                    throw new Exception("La contraseña es requerida para nuevos usuarios");
                }
                $data['contrasena'] = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

                $sql = "INSERT INTO usuarios (nombre_usuario, contrasena, correo_electronico, rol, numero_telefono, descripcion, esta_activo) 
                        VALUES (:nombre_usuario, :contrasena, :correo_electronico, :rol, :numero_telefono, :descripcion, :esta_activo)";
            } else {
                $sql = "UPDATE usuarios SET 
                        nombre_usuario = :nombre_usuario,
                        correo_electronico = :correo_electronico,
                        rol = :rol,
                        numero_telefono = :numero_telefono,
                        descripcion = :descripcion,
                        esta_activo = :esta_activo";

                if (!empty($_POST['contrasena'])) {
                    $data['contrasena'] = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
                    $sql .= ", contrasena = :contrasena";
                }

                $sql .= " WHERE id = :id";
                $data['id'] = $_POST['usuario_id'];
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($data);

            echo json_encode(['success' => true]);
            break;

        case 'delete':
            if ($_SESSION['rol'] !== 'Administrador') {
                throw new Exception("Solo el administrador puede eliminar usuarios");
            }

            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$_POST['usuario_id']]);
            echo json_encode(['success' => true]);
            break;

        case 'recover':
            $newPassword = bin2hex(random_bytes(4)); // Genera una contraseña aleatoria
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $_POST['usuario_id']]);

            // Aquí deberías implementar el envío por correo
            echo json_encode([
                'success' => true,
                'new_password' => $newPassword,
                'message' => 'Contraseña generada: ' . $newPassword
            ]);
            break;

        default:
            throw new Exception("Acción no válida");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
