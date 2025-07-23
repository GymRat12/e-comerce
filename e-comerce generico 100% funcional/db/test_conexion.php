<?php
$host = 'localhost';
$db = 'ecommerce';
$user = 'cibertec';
$pass = 'DavalosSanchez12*';        // Agrega tu contraseña si tienes

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
?>