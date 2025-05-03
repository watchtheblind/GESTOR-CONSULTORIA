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

        $inactive = 3600; // 1 hora en segundos
        if (time() - $_SESSION['last_activity'] > $inactive) {
            session_unset();
            session_destroy();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }
}
