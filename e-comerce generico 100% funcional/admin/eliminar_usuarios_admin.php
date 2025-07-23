<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

require_once("../db/conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["usuario_id"])) {
    $usuario_id = $_POST["usuario_id"];

    // Verificar que el usuario existe
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        echo "<script>alert('Usuario no encontrado'); window.location.href='admin_usuarios.php';</script>";
        exit;
    }

    // Eliminar usuario
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    if ($stmt->execute([$usuario_id])) {
        echo "<script>alert('✅ Usuario eliminado correctamente'); window.location.href='admin_usuarios.php';</script>";

    } 
    if ($usuario_id == $_SESSION['admin_id']) {
    echo "<script>alert('No puedes eliminar tu propia cuenta'); window.location.href='admin_usuarios.php';</script>";
    exit;
}
else {
        echo "<script>alert('❌ Error al eliminar el usuario'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Solicitud inválida'); window.location.href='admin_usuarios.php';</script>";
}
