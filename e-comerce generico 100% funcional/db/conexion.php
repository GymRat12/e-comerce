<?php
$host = 'localhost';
$db = 'ecommerce';
$user = 'cibertec';
$pass = 'DavalosSanchez12*'; // Cambia esto si tienes contraseña en tu MySQL

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
