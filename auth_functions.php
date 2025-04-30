<?php

// Función para verificar sesión
function isLoggedIn()
{
    return isset($_SESSION['user_id']) &&
        !empty($_SESSION['user_id']) &&
        isset($_SESSION['last_activity']);
}

// Verificar inactividad (30 minutos)
function checkSessionExpiration()
{
    $inactivity = 1800; // 30 minutos
    if (
        isset($_SESSION['last_activity']) &&
        (time() - $_SESSION['last_activity'] > $inactivity)
    ) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}
