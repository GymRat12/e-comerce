<?php
require_once("../db/conexion.php");
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pedidos realizados</title>
  <link rel="stylesheet" href="../public/css/admin_verPedidos.css">
  <style>
    body {
      font-family: Arial;
      padding: 20px;
    }
    h1 {
      text-align: center;
    }
    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 30px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #f4f4f4;
    }
    .pedido {
      background-color: #e9f9ff;
      padding: 15px;
      margin-bottom: 25px;
      border-radius: 10px;
    }
    .filtros {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
<?php include("admin_header.php"); ?>

<h1>📦 Pedidos Realizados</h1>

<!-- Exportar a Excel -->
<form action="exportar_pedidos.php" method="GET" class="filtros">
  <label>Desde:</label>
  <input type="date" name="desde" required>
  <label>Hasta:</label>
  <input type="date" name="hasta" required>
  <button type="submit">📥 Exportar a Excel</button>
</form>

<!-- Filtro por estado -->
<!-- Filtros (Estado + Buscar) alineados horizontalmente -->
<div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">

  <!-- Filtro por estado -->
  <form method="GET" action="ver_pedidos.php" style="display: flex; align-items: center; gap: 10px;">
    <label for="estado">Filtrar por estado:</label>
    <select name="estado" id="estado">
      <option value="">-- Todos --</option>
      <option value="pendiente" <?= (isset($_GET['estado']) && $_GET['estado'] === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
      <option value="en proceso" <?= (isset($_GET['estado']) && $_GET['estado'] === 'en proceso') ? 'selected' : '' ?>>En proceso</option>
      <option value="enviado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'enviado') ? 'selected' : '' ?>>Enviado</option>
      <option value="completado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'completado') ? 'selected' : '' ?>>Completado</option>
      <option value="cancelado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
    </select>
    <button type="submit">Filtrar estado</button>
  </form>

  <!-- Buscador -->
  <form method="GET" action="ver_pedidos.php" style="display: flex; align-items: center; gap: 10px;">
    <label for="buscar">Buscar:</label>
    <input type="text" name="buscar" id="buscar" placeholder="Nombre, DNI o teléfono" value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
    <button type="submit">Buscar</button>
  </form>

</div>

<?php
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

$query = "
    SELECT p.*, u.nombre AS usuario_nombre, u.correo AS usuario_correo, u.dni AS usuario_dni
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE 1=1
";
$params = [];

if ($estado !== '') {
    $query .= " AND p.estado = ?";
    $params[] = $estado;
}

if ($buscar !== '') {
    $query .= " AND (
        u.nombre LIKE ? OR 
        u.dni LIKE ? OR 
        p.telefono LIKE ?
    )";
    $busca = "%$buscar%";
    $params[] = $busca;
    $params[] = $busca;
    $params[] = $busca;
}

$query .= " ORDER BY p.fecha DESC";
$stmt = $conexion->prepare($query);
$stmt->execute($params);
$pedidos = $stmt;

while ($pedido = $pedidos->fetch(PDO::FETCH_ASSOC)) {
    echo "<div class='pedido'>";
    $estadoTexto = strtoupper($pedido['estado']);

    echo "<form method='POST' action='actualizar_estado.php'>";
    echo "<h3>🧾 Pedido #{$pedido['id']} | Total: S/ {$pedido['total']} | Método: {$pedido['metodo_pago']} | Fecha: {$pedido['fecha']}</h3>";

    echo "<input type='hidden' name='pedido_id' value='{$pedido['id']}'>";
    echo "Estado: <select name='estado'>
            <option value='pendiente' " . ($pedido['estado'] === 'pendiente' ? 'selected' : '') . ">Pendiente</option>
            <option value='en proceso' " . ($pedido['estado'] === 'en proceso' ? 'selected' : '') . ">En proceso</option>
            <option value='enviado' " . ($pedido['estado'] === 'enviado' ? 'selected' : '') . ">Enviado</option>
            <option value='completado' " . ($pedido['estado'] === 'completado' ? 'selected' : '') . ">Completado</option>
            <option value='cancelado' " . ($pedido['estado'] === 'cancelado' ? 'selected' : '') . ">Cancelado</option>
          </select>";
    echo " <button type='submit'>Actualizar estado</button>";

    echo "<p>👤 Cliente: <strong>{$pedido['usuario_nombre']}</strong></p>";
    echo "<p>📧 Correo: {$pedido['usuario_correo']}</p>";
    echo "<p>🆔 DNI: {$pedido['usuario_dni']}</p>";
    echo "<p>📞 Teléfono: {$pedido['telefono']}</p>";

    if (!empty($pedido['direccion'])) {
        echo "<p>📍 Dirección: {$pedido['direccion']}</p>";
    }

    echo "</form>";

    echo "<p>📌 Estado: <strong>{$estadoTexto}</strong></p>";

    if ($pedido['metodo_pago'] === 'Yape' && !empty($pedido['comprobante'])) {
        echo "<p>📎 Comprobante: <a href='../public/img/comprobantes/{$pedido['comprobante']}' target='_blank'>Ver comprobante</a></p>";
    }

    if ($pedido['metodo_pago'] === 'Yape' && $pedido['estado'] === 'pendiente') {
        echo "<form method='GET' action='validar_pedido.php'>
                <input type='hidden' name='id' value='{$pedido['id']}'>
                <button type='submit'>✅ Validar Pedido</button>
              </form>";
    }

    $stmtDetalles = $conexion->prepare("SELECT p.nombre, dp.cantidad, dp.precio_unitario
                                        FROM detalle_pedido dp
                                        JOIN productos p ON dp.producto_id = p.id
                                        WHERE dp.pedido_id = ?");
    $stmtDetalles->execute([$pedido['id']]);
    $detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

    echo "<table>";
    echo "<tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr>";
    foreach ($detalles as $item) {
        $subtotal = $item['cantidad'] * $item['precio_unitario'];
        echo "<tr>
                <td>{$item['nombre']}</td>
                <td>{$item['cantidad']}</td>
                <td>S/ {$item['precio_unitario']}</td>
                <td>S/ " . number_format($subtotal, 2) . "</td>
              </tr>";
    }
    echo "</table>";
    echo "</div><br>";
}
?>

</body>
</html>
