<?php
// index.php – Demo de estado de login

// Variable de demo: 0 = no logueado, 1 = logueado
$loggedIn = 1; // Cambia a 1 para simular usuario logueado

// Cabecera y apertura de layout
include 'header.php';

// Lógica condicional
if ($loggedIn === 0) {
    // Usuario NO logueado: mostrar formulario de login
    include 'login.php';
} else {
    // Usuario logueado: mostrar panel
    include 'sidebar.php';
    include 'dashboard.php';
}

// Pie de página
include 'footer.php';
