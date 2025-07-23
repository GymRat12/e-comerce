<?php
session_start();
require_once("../db/conexion.php");

// Verifica que el usuario esté logueado
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo "<script>alert('Debes iniciar sesión para ver tus pedidos.'); window.location.href='../usuario_login/login.php';</script>";
  exit;
}

// Obtener pedidos del usuario
$stmt = $conexion->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC");
$stmt->execute([$usuario_id]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Pedidos</title>
  <link rel="stylesheet" href="css/mis_pedidos.css">
  <link rel="stylesheet" href="css/header.css">
</head>
<body>
  <?php include("partials/header.php"); ?>

  <h1>📦 Mis Pedidos</h1>

  <?php if (empty($pedidos)): ?>
    <p>No tienes pedidos aún.</p>
  <?php else: ?>
    <?php foreach ($pedidos as $pedido): ?>
      <div class="pedido-box">
        <strong>Pedido #<?= $pedido['id'] ?></strong><br>
        Fecha: <?= $pedido['fecha'] ?><br>
        Total: S/ <?= number_format($pedido['total'], 2) ?><br>
        Pago: <?= ucfirst($pedido['metodo_pago']) ?><br>
        Estado: <?= ucfirst($pedido['estado']) ?><br>

        <details>
          <summary>Ver productos</summary>
          <ul>
            <?php
            $stmtDetalle = $conexion->prepare("
              SELECT p.nombre, d.cantidad, d.precio_unitario
              FROM detalle_pedido d
              JOIN productos p ON p.id = d.producto_id
              WHERE d.pedido_id = ?
            ");
            $stmtDetalle->execute([$pedido['id']]);
            $productos = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
            foreach ($productos as $prod):
            ?>
              <li><?= $prod['nombre'] ?> x<?= $prod['cantidad'] ?> - S/ <?= number_format($prod['precio_unitario'], 2) ?></li>
            <?php endforeach; ?>
          </ul>
        </details>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</body>
</html>
