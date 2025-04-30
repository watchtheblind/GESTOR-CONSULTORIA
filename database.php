<?php
$nombreHost = 'localhost';
$baseDatos = 'consultorias';
$usuario = 'root';
$contrasena = "";
try {
    $conn = new PDO("mysql:host=$nombreHost;dbname=$baseDatos", $usuario, $contrasena);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
