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

// Cambiar estado a inactivo en vez de eliminar físicamente
$stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
$stmt->execute([$id]);

echo "<script>alert('🗑️ Producto Elmiminado'); window.location.href='productos.php';</script>";
