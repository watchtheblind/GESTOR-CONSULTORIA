<?php
// index.php - Punto de entrada único
session_start();
require_once 'database.php';

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

// Redirigir lógica y carga de vista
if (isLoggedIn() && checkSessionExpiration()) {
    // Usuario autenticado, mostrar dashboard con header y footer
    include 'header.php';
    include 'dashboard.php';
    include 'footer.php';
} else {
    // Usuario no autenticado o sesión expirada, mostrar solo login
    include 'header2.php';
    include 'login.php';
}
