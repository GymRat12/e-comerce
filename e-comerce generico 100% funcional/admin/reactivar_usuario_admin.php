<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

require_once("../db/conexion.php");

$usuario_id = $_POST['usuario_id'] ?? null;

if ($usuario_id && is_numeric($usuario_id)) {
    $stmt = $conexion->prepare("UPDATE usuarios SET activo = 1 WHERE id = ?");
    $stmt->execute([$usuario_id]);
}

header("Location: admin_usuarios.php");
exit;
