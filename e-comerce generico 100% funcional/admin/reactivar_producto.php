<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

require_once("../db/conexion.php");

if (!isset($_GET['id'])) {
    echo "❌ ID de producto no proporcionado.";
    exit;
}

$id = $_GET['id'];

$stmt = $conexion->prepare("UPDATE productos SET activo = 1 WHERE id = ?");
$stmt->execute([$id]);

echo "<script>alert('✅ Producto reactivado correctamente'); window.location.href='productos.php';</script>";
