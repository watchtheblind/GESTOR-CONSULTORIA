<?php
// index.php - Punto de entrada único
session_start();
require_once 'database.php';
require_once 'auth_functions.php';

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
