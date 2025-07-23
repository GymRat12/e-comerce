<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<?php
require_once("../db/conexion.php");

// Obtener productos
$estado = $_GET['estado'] ?? 'todos';

if ($estado === 'activos') {
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE activo = 1");
    $stmt->execute();
} elseif ($estado === 'inactivos') {
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE activo = 0");
    $stmt->execute();
} else {
    $stmt = $conexion->prepare("SELECT * FROM productos");
    $stmt->execute();
}

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Productos - Admin</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
    }
    img {
      height: 60px;
    }
  </style>
    <link rel="stylesheet" href="../public/css/admin_productos.css">
</head>
<body>
  <?php include("admin_header.php"); ?>
  <h1>🛠️ Panel de administración - Productos</h1>
  <a href="agregar_producto.php">➕ Agregar nuevo producto</a>
  <br><br>
  
<form method="GET" style="margin-bottom: 20px;">
  <label>Filtrar productos:</label>
  <select name="estado">
    <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>Todos</option>
    <option value="activos" <?= $estado === 'activos' ? 'selected' : '' ?>>Activos</option>
    <option value="inactivos" <?= $estado === 'inactivos' ? 'selected' : '' ?>>Inactivos</option>
  </select>
  <button type="submit">Aplicar filtro</button>
</form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Imagen</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($productos as $p): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= $p['nombre'] ?></td>
          <td>S/ <?= $p['precio'] ?></td>
          <td><?= $p['stock'] ?></td>
          <td><img src="../public/img/<?= $p['imagen'] ?>" alt=""></td>
          <td>
  <?php if ($p['activo']): ?>
    <a href="desactivar_producto.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Estás seguro de desactivar este producto?')">🗑️ Desactivar</a>
  <?php else: ?>
    <a href="reactivar_producto.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Reactivar este producto?')">✅ Reactivar</a>
  <?php endif; ?>
</td>
          <td>
            <a href="editar_producto.php?id=<?= $p['id'] ?>">✏️ Editar</a> |
            <a href="eliminar_producto.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Estás seguro?')">🗑️ Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
