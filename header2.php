<?php
// header.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">

    <title>Mi Aplicación</title>

</head>

<body>
    <div class="container">
        <header>
            <div class="branding">
                <a href="/" aria-label="Inicio">
                    <!-- SVG embebido -->
                    <svg width="120" height="40" viewBox="0 0 120 40" xmlns="http://www.w3.org/2000/svg">
                        <rect width="120" height="40" rx="8" ry="8" fill="#004080" />
                        <circle cx="20" cy="20" r="12" fill="#ffd700" />
                        <text x="40" y="25" font-family="Arial, sans-serif" font-size="18" fill="#fff">MiApp</text>
                    </svg>
                </a>
            </div>
            <span class="menu-toggle" id="menuToggle">☰</span>
            <nav class="top-actions">
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Características</a></li>
                    <li><a href="#">Precios</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>
        </header>
        <div class="layout">