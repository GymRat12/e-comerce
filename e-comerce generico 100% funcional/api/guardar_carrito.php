<?php
session_start();
require_once("../db/conexion.php");

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo json_encode(["success" => false, "mensaje" => "Usuario no autenticado"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$productos = $data["productos"] ?? [];

try {
  // Ver si ya existe un carrito para este usuario
  $stmt = $conexion->prepare("SELECT id FROM carritos WHERE usuario_id = ?");
  $stmt->execute([$usuario_id]);
  $carrito = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($carrito) {
    $carrito_id = $carrito["id"];

    // Limpiar items previos
    $conexion->prepare("DELETE FROM carrito_items WHERE carrito_id = ?")->execute([$carrito_id]);
  } else {
    // Crear nuevo carrito
    $stmt = $conexion->prepare("INSERT INTO carritos (usuario_id) VALUES (?)");
    $stmt->execute([$usuario_id]);
    $carrito_id = $conexion->lastInsertId();
  }

  // Insertar los productos
  $stmt_item = $conexion->prepare("INSERT INTO carrito_items (carrito_id, producto_id, cantidad) VALUES (?, ?, ?)");
  foreach ($productos as $p) {
    $stmt_item->execute([$carrito_id, $p["id"], $p["cantidad"]]);
  }

  echo json_encode(["success" => true]);
} catch (Exception $e) {
  echo json_encode(["success" => false, "mensaje" => $e->getMessage()]);
}
