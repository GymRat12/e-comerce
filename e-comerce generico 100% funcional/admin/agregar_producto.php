<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
?>
<?php
require_once("../db/conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];

    // Procesar imagen
    $imagen_nombre = $_FILES["imagen"]["name"];
    $imagen_tmp = $_FILES["imagen"]["tmp_name"];
    $ruta_destino = "../public/img/" . $imagen_nombre;

    move_uploaded_file($imagen_tmp, $ruta_destino);

    // Guardar en base de datos
    $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $precio, $imagen_nombre, $stock]);

    echo "<script>alert('✅ Producto agregado correctamente'); window.location.href='agregar_producto.php';</script>";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar producto</title>
  <link rel="stylesheet" href="../public/css/admin_agregarproducto.css">
</head>
<body>
  <?php include("admin_header.php"); ?>

  <div class="form-container">
    <h1 class="titulo-formulario">➕ Agregar nuevo producto</h1>

    <form action="" method="POST" enctype="multipart/form-data">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" class="input-form" required>

      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" name="descripcion" class="input-form" rows="4" required></textarea>

      <label for="precio">Precio (S/):</label>
      <input type="number" step="0.01" id="precio" name="precio" class="input-form" required>

      <label for="stock">Stock:</label>
      <input type="number" id="stock" name="stock" class="input-form" required>

      <label for="imagen">Imagen del producto:</label>
      <input type="file" id="imagen" name="imagen" class="input-form" accept="image/*" required>

      <button type="submit" class="boton-guardar">💾 Guardar producto</button>
    </form>
  </div>
</body>
</html>
