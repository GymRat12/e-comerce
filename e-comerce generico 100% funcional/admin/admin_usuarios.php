<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

require_once("../db/conexion.php");

// Obtener todos los usuarios
$estado = $_GET['estado'] ?? 'todos';

if ($estado === 'activos') {
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE activo = 1 ORDER BY id DESC");
} elseif ($estado === 'inactivos') {
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE activo = 0 ORDER BY id DESC");
} else {
    $stmt = $conexion->prepare("SELECT * FROM usuarios ORDER BY activo DESC, id DESC");
}

$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalUsuarios = count($usuarios);

$mensajeFiltro = match ($estado) {
  'activos' => "usuarios activos",
  'inactivos' => "usuarios inactivos",
  default => "usuarios en total"
};

if (!$usuarios) {
    echo "<p>No hay usuarios registrados.</p>";
    exit;
}



$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios Registrados - Admin</title>
  <link rel="stylesheet" href="../public/css/admin.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    h1 {
      text-align: center;
      margin: 40px 0 20px;
      color: #2c3e50;
    }

    .tabla-contenedor {
      width: 90%;
      max-width: 1000px;
      margin: auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      padding: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th {
      background-color: #2c3e50;
      color: white;
      padding: 12px;
      text-align: left;
    }

    td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      color: #333;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    a {
      text-decoration: none;
      color: #2980b9;
      font-weight: bold;
    }

    a:hover {
      color: #1abc9c;
    }
  </style>
</head>
<body>
  <?php include("admin_header.php"); ?>

  <h1>👥 Usuarios registrados</h1>
<form method="GET" style="text-align: center; margin-bottom: 20px;">
  <label for="estado">Filtrar por estado:</label>
  <select name="estado" id="estado">
    <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>Todos</option>
    <option value="activos" <?= $estado === 'activos' ? 'selected' : '' ?>>Activos</option>
    <option value="inactivos" <?= $estado === 'inactivos' ? 'selected' : '' ?>>Inactivos</option>
  </select>
  <button type="submit">Aplicar</button>
</form>
<div style="text-align:center; margin-bottom: 10px; font-size: 18px; color: #2c3e50;">
  🔢 Hay <?= $totalUsuarios ?> <?= $mensajeFiltro ?>
</div>

  <div class="tabla-contenedor">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>DNI</th>
          <th>Opciones</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nombre']) ?></td>
            <td><?= htmlspecialchars($u['correo']) ?></td>
            <td><?= htmlspecialchars($u['dni']) ?></td>
            
            <td>
  <a href="ver_usuario_admin.php?id=<?= $u['id'] ?>">🔍 Ver</a>
  <?php if ($u['activo'] == 0): ?>
    | <form action="reactivar_usuario_admin.php" method="POST" style="display:inline;">
        <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
        <button type="submit" style="color:green; border:none; background:none; cursor:pointer;">♻️ Reactivar</button>
      </form>
  <?php endif; ?>
</td>

<td style="color: <?= $u['activo'] ? 'green' : 'gray' ?>;">
  <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
</td>

        

          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
