<?php
session_start();
require_once 'auth_functions.php';
require_once 'database.php';

// Verificar permisos (solo admin)
if ($_SESSION['rol'] !== 'Administrador') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Configuraciones a guardar
        $configuraciones = [
            'correo_administrativo' => $_POST['correo_administrativo'],
            'marca_empresa' => $_POST['marca_empresa'],
            'telefono_contacto' => $_POST['telefono_contacto'],
            'direccion_empresa' => $_POST['direccion_empresa'],
            'descripcion_empresa' => $_POST['descripcion_empresa'],
            'tiempo_sesion' => $_POST['tiempo_sesion']
        ];

        // Procesar el logo si se subió
        if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
            $directorio = 'uploads/logos/';
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $nombre_archivo = uniqid() . '_' . basename($_FILES['logo_empresa']['name']);
            $ruta_completa = $directorio . $nombre_archivo;

            if (move_uploaded_file($_FILES['logo_empresa']['tmp_name'], $ruta_completa)) {
                $configuraciones['logo_empresa'] = $ruta_completa;
            }
        }

        // Guardar cada configuración
        $stmt = $conn->prepare("INSERT INTO configuraciones_sistema (clave, valor, descripcion) 
                               VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE valor = ?, descripcion = ?");

        foreach ($configuraciones as $clave => $valor) {
            $descripcion = obtener_descripcion($clave);
            $stmt->execute([$clave, $valor, $descripcion, $valor, $descripcion]);
        }

        // Registrar en el historial
        $stmt = $conn->prepare("INSERT INTO historial_cambios (usuario_id, accion, descripcion) VALUES (?, ?, ?)");
        $stmt->execute([
            $_SESSION['usuario_id'],
            'Actualización de Configuración',
            'Se actualizaron las configuraciones del sistema'
        ]);

        $_SESSION['mensaje'] = "Configuraciones guardadas exitosamente";
        $_SESSION['tipo_mensaje'] = "success";
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "Error al guardar las configuraciones: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "danger";
    }

    header('Location: configuracion.php');
    exit();
}

function obtener_descripcion($clave)
{
    $descripciones = [
        'correo_administrativo' => 'Correo electrónico principal para notificaciones administrativas',
        'marca_empresa' => 'Nombre o marca de la empresa',
        'telefono_contacto' => 'Número de teléfono de contacto principal',
        'direccion_empresa' => 'Dirección física de la empresa',
        'logo_empresa' => 'Ruta del archivo del logo de la empresa',
        'descripcion_empresa' => 'Descripción general de la empresa',
        'tiempo_sesion' => 'Duración de la sesión en minutos (5-120)'
    ];

    return $descripciones[$clave] ?? 'Configuración del sistema';
}
