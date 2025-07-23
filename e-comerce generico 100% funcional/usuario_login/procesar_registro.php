<?php
require_once("../db/conexion.php");

$nombre = $_POST["nombre"];
$correo = $_POST["correo"];
$dni = $_POST["dni"];
$contraseña = password_hash($_POST["contraseña"], PASSWORD_DEFAULT);

try {
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, dni, contraseña) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $correo, $dni, $contraseña]);

    echo "<script>alert('✅ Registro exitoso. Ahora puedes iniciar sesión'); window.location.href='login.php';</script>";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'correo')) {
        $mensaje = "❌ Error: ya existe un usuario con ese correo.";
    } elseif (str_contains($e->getMessage(), 'dni')) {
        $mensaje = "❌ Error: ya existe un usuario con ese DNI.";
    } else {
        $mensaje = "❌ Error en el registro.";
    }
    echo "<script>alert('$mensaje'); window.location.href='registro.php';</script>";
}
?>
