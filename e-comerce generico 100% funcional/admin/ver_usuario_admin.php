<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

require_once("../db/conexion.php");

// Validar ID del usuario
$usuario_id = $_GET['id'] ?? null;
if (!$usuario_id || !is_numeric($usuario_id)) {
    echo "ID de usuario inválido.";
    exit;
}

// Obtener datos del usuario
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalles del Usuario</title>
  <link rel="stylesheet" href="../public/css/admin.css">
  <style>
    body {
      background-color: #f7f9fc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    .contenedor {
      max-width: 600px;
      margin: 40px auto;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      padding: 30px 40px;
      animation: fadeIn 0.5s ease-in-out;
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
    }

    p {
      font-size: 17px;
      margin: 12px 0;
      color: #34495e;
      border-bottom: 1px solid #eee;
      padding-bottom: 8px;
    }

    p strong {
      color: #2d3436;
    }

    a {
      display: inline-block;
      margin-top: 25px;
      text-decoration: none;
      color: #2980b9;
      font-weight: bold;
      transition: color 0.3s;
    }

    a:hover {
      color: #1abc9c;
    }

    form button {
      display: block;
      width: 100%;
      background-color: #e74c3c;
      color: white;
      padding: 12px;
      border: none;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 20px;
      transition: background-color 0.3s ease;
    }

    form button:hover {
      background-color: #c0392b;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <?php include("admin_header.php"); ?>

  <div class="contenedor">
    <h2>👤 Detalles del Usuario</h2>
    <p><strong>ID:</strong> <?= $usuario['id'] ?></p>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
    <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?></p>
    <p><strong>DNI:</strong> <?= htmlspecialchars($usuario['dni']) ?></p>

    <form action="eliminar_usuarios_admin.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario? Se borraran los registros de compra y esta acción no se puede deshacer.');">
      <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>">
      <button type="submit">🗑️ Eliminar Usuario</button>
    </form>

    <form action="desactivar_usuario_admin.php" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar este usuario?');">
  <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>">
  <button type="submit" style="background-color: orange; color: white; padding: 10px; border: none; border-radius: 5px; margin-top: 20px;">
    🚫 Desactivar Usuario
  </button>
</form>

    <a href="admin_usuarios.php">⬅️ Volver a la lista de usuarios</a>
  </div>
</body>
</html>
