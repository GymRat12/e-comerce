<?php
session_start();
require_once("../db/conexion.php");

$correo = $_POST["correo"];
$claveIngresada = $_POST["contraseña"];

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmt->execute([$correo]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Verifica si la contraseña es correcta
    if (password_verify($claveIngresada, $usuario["contraseña"])) {
        // Verifica si el usuario está activo
        if ($usuario["activo"] == 1) {
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];
            header("Location: ../public/index.php");
            exit;
        } else {
            echo "<script>alert('❌ Tu cuenta está desactivada. Contacta al administrador.'); window.location.href='login.php';</script>";
            exit;
        }
    }
}

// Si no pasa ninguna validación
echo "<script>alert('❌ Credenciales incorrectas'); window.location.href='login.php';</script>";
