<?php
// Verificar si la sesión ya está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si la función ya existe antes de declararla
if (!function_exists('isLoggedIn')) {
    function isLoggedIn()
    {
        return isset($_SESSION['id']) &&
            !empty($_SESSION['id']) &&
            isset($_SESSION['last_activity']);
    }
}

if (!function_exists('checkSessionExpiration')) {
    function checkSessionExpiration()
    {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return true;
        }

        // Obtener el tiempo de sesión desde la base de datos
        global $conn;
        try {
            $stmt = $conn->query("SELECT valor FROM configuraciones_sistema WHERE clave = 'tiempo_sesion'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $inactive = ($result ? intval($result['valor']) : 30) * 60; // Convertir minutos a segundos
        } catch (PDOException $e) {
            $inactive = 30 * 60; // Valor por defecto: 30 minutos
        }

        if (time() - $_SESSION['last_activity'] > $inactive) {
            session_unset();
            session_destroy();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }
}
