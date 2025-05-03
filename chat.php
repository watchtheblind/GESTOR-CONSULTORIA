<?php
session_start();
require_once 'database.php';
require_once 'auth_functions.php';

if (!isset($_SESSION['id']) || !isset($_GET['receptor_id'])) {
    header('Location: index.php');
    exit();
}

$remitente_id = $_SESSION['id'];
$receptor_id = $_GET['receptor_id'];

// Obtener información del receptor
$query = "SELECT nombre_usuario FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$receptor_id]);
$receptor = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener mensajes
$query = "SELECT m.*, u.nombre_usuario as remitente_nombre 
          FROM mensajes_chat m 
          JOIN usuarios u ON m.remitente_id = u.id 
          WHERE (remitente_id = ? AND receptor_id = ?) 
          OR (remitente_id = ? AND receptor_id = ?) 
          ORDER BY enviado_en ASC";
$stmt = $conn->prepare($query);
$stmt->execute([$remitente_id, $receptor_id, $receptor_id, $remitente_id]);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?php echo htmlspecialchars($receptor['nombre_usuario']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container {
            height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .back-button {
            margin-right: 1rem;
            padding: 0.5rem 1rem;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .back-button:hover {
            background: #0b5ed7;
            color: white;
        }

        .chat-title {
            margin: 0;
            flex-grow: 1;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            background: #f9f9f9;
        }

        .message {
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            max-width: 70%;
        }

        .message-sent {
            background: #3498db;
            color: white;
            margin-left: auto;
        }

        .message-received {
            background: #e9ecef;
            color: #212529;
        }

        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .message-form {
            padding: 1rem;
            background: #fff;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="chat-container">
        <div class="chat-header">
            <a href="usuarios.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <h2 class="chat-title">Chat con <?php echo htmlspecialchars($receptor['nombre_usuario']); ?></h2>
        </div>

        <div class="chat-messages">
            <?php foreach ($mensajes as $mensaje): ?>
                <div class="message <?php echo $mensaje['remitente_id'] == $remitente_id ? 'message-sent' : 'message-received'; ?>">
                    <div class="message-content"><?php echo htmlspecialchars($mensaje['mensaje']); ?></div>
                    <div class="message-time">
                        <?php echo date('H:i', strtotime($mensaje['enviado_en'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form class="message-form" action="enviar_mensaje.php" method="POST">
            <input type="hidden" name="receptor_id" value="<?php echo $receptor_id; ?>">
            <div class="input-group">
                <input type="text" name="mensaje" class="form-control" placeholder="Escribe tu mensaje..." required>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>

</html>