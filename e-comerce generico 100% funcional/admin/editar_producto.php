<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<?php
require_once("../db/conexion.php");

// 1. Obtener ID del producto
if (!isset($_GET['id'])) {
    echo "❌ ID de producto no proporcionado.";
    exit;
}

$id = $_GET['id'];

// 2. Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    
    // Verificar si se subió una nueva imagen
    if (!empty($_FILES["imagen"]["name"])) {
        $imagen_nombre = $_FILES["imagen"]["name"];
        $imagen_tmp = $_FILES["imagen"]["tmp_name"];
        $ruta_destino = "../public/img/" . $imagen_nombre;
        move_uploaded_file($imagen_tmp, $ruta_destino);

        $stmt = $conexion->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, imagen=? WHERE id=?");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen_nombre, $id]);
    } else {
        // Sin imagen nueva
        $stmt = $conexion->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=? WHERE id=?");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $id]);
    }

    echo "<script>alert('✅ Producto actualizado'); window.location.href='productos.php';</script>";
    exit;
}

// 3. Obtener datos actuales
$stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    echo "❌ Producto no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar producto</title>
  <link rel="stylesheet" href="../public/css/admin_editarproducto.css">
</head>
<body>
  <?php include("admin_header.php"); ?>

  <div class="form-container">
    <h1 class="titulo-formulario">✏️ Editar producto: <?= htmlspecialchars($producto['nombre']) ?></h1>

    <form action="" method="POST" enctype="multipart/form-data">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" class="input-form" value="<?= $producto['nombre'] ?>" required>

      <label for="descripcion">Descripción:</label>
      <textarea id="descripcion" name="descripcion" class="input-form" rows="4" required><?= $producto['descripcion'] ?></textarea>

      <label for="precio">Precio (S/):</label>
      <input type="number" step="0.01" id="precio" name="precio" class="input-form" value="<?= $producto['precio'] ?>" required>

      <label for="stock">Stock:</label>
      <input type="number" id="stock" name="stock" class="input-form" value="<?= $producto['stock'] ?>" required>

      <label>Imagen actual:</label><br>
      <img src="../public/img/<?= $producto['imagen'] ?>" class="imagen-actual"><br><br>

      <label for="imagen">Subir nueva imagen (opcional):</label>
      <input type="file" id="imagen" name="imagen" class="input-form" accept="image/*">

      <button type="submit" class="boton-guardar">💾 Guardar cambios</button>
    </form>

    <div class="volver">
      <a href="productos.php">← Volver a productos</a>
    </div>
  </div>
</body>
</html>

