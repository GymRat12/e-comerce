<?php
session_start();
require_once("../db/conexion.php");

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo json_encode([]);
  exit;
}

$stmt = $conexion->prepare("
  SELECT p.id, p.nombre, p.precio, ci.cantidad 
  FROM carrito_items ci
  JOIN carritos c ON ci.carrito_id = c.id
  JOIN productos p ON ci.producto_id = p.id
  WHERE c.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($items);
