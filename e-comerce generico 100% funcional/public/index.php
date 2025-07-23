<?php
session_start();
include("../db/conexion.php");
$usuario_id = $_SESSION["usuario_id"] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tienda de Zapatillas</title>
  <link rel="stylesheet" href="css/estilos.css">
  <link rel="stylesheet" href="css/header.css">
</head>
<body>
  <?php include("partials/header.php"); ?>

  <h1>Catálogo de Zapatillas</h1>

  <div class="productos">
  <?php
    $stmt = $conexion->query("SELECT * FROM productos WHERE activo = 1 AND stock > 0");
    while ($producto = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo "<div class='producto'>";
      echo "<img src='img/{$producto['imagen']}' width='150'>";
      echo "<h3>{$producto['nombre']}</h3>";
      echo "<p>S/ {$producto['precio']}</p>";
      echo "<a href='producto.php?id={$producto['id']}'>Ver más</a>";

      // Botón para agregar al carrito
      echo "<button 
              class='btn-agregar' 
              data-id='{$producto['id']}'
              data-nombre='" . htmlspecialchars($producto['nombre'], ENT_QUOTES) . "'
              data-precio='{$producto['precio']}'
            >
              Agregar al carrito
            </button>";
      echo "</div>";
    }
  ?>
  </div>

  <script>
    const usuarioId = <?= json_encode($usuario_id) ?>;
  </script>
  <script src="js/carrito.js"></script>

  <footer>
    <p>&copy; 2023 Tienda de Zapatillas. Todos los derechos reservados.</p>
  </footer>
</body>
</html>
