<?php
session_start();
require_once("../db/conexion.php");

// Verificar usuario logueado
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    if (isset($_POST["desdeYape"])) {
        echo "<script>alert('Debes iniciar sesión para hacer el pedido.'); window.location.href='../usuario_login/index.php';</script>";
    } else {
        echo json_encode(["success" => false, "mensaje" => "Usuario no autenticado"]);
    }
    exit;
}

// ----------- CASO YAPE -------------
if (isset($_POST["desdeYape"])) {
    $productos = json_decode($_POST["datosCarrito"], true);
    $direccion = $_POST["direccion"] ?? null;
    $telefono = $_POST["telefono"] ?? null;

    $total = 0;
    foreach ($productos as $p) {
        $total += $p["precio"] * $p["cantidad"];
    }

    // Validar stock antes de guardar
    foreach ($productos as $p) {
        $stmt = $conexion->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt->execute([$p["id"]]);
        $row = $stmt->fetch();
        if (!$row || $row["stock"] < $p["cantidad"]) {
            echo "<script>alert('❌ Stock insuficiente para el producto: " . $p["nombre"] . "'); window.history.back();</script>";
            exit;
        }
    }

    // Subir comprobante
    $nombreComprobante = null;
    if (isset($_FILES["comprobante"])) {
        $nombreComprobante = time() . "_" . $_FILES["comprobante"]["name"];
        $tmp = $_FILES["comprobante"]["tmp_name"];
        move_uploaded_file($tmp, "../public/img/comprobantes/" . $nombreComprobante);
    }

    try {
        $conexion->beginTransaction();

        // Insertar pedido
        $stmt = $conexion->prepare("INSERT INTO pedidos (usuario_id, total, estado, metodo_pago, comprobante, direccion, telefono) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $total, "pendiente", "Yape", $nombreComprobante, $direccion, $telefono]);
        $pedido_id = $conexion->lastInsertId();

        // Insertar detalle y descontar stock
        $stmt_detalle = $conexion->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        $stmt_stock = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        foreach ($productos as $p) {
            $stmt_detalle->execute([$pedido_id, $p["id"], $p["cantidad"], $p["precio"]]);
            $stmt_stock->execute([$p["cantidad"], $p["id"]]);
        }

        $conexion->commit();
        echo "<script>alert('✅ Pedido recibido. Esperando validación de Yape.'); window.location.href='../public/index.php';</script>";
        exit;
    } catch (Exception $e) {
        $conexion->rollBack();
        echo "<script>alert('❌ Error al registrar el pedido: " . $e->getMessage() . "'); window.history.back();</script>";
    }
    exit;
}

// ----------- CASO PAYPAL -------------
$data = json_decode(file_get_contents("php://input"), true);
$productos = $data['productos'];
$total = $data['total'];
$direccion = $data['direccion'] ?? null;
$telefono = $data['telefono'] ?? null;

// Validar stock antes de guardar
foreach ($productos as $p) {
    $stmt = $conexion->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$p["id"]]);
    $row = $stmt->fetch();
    if (!$row || $row["stock"] < $p["cantidad"]) {
        echo json_encode([
            "success" => false,
            "mensaje" => "❌ Stock insuficiente para el producto: " . $p["nombre"]
        ]);
        exit;
    }
}

try {
    $conexion->beginTransaction();

    // Insertar pedido
    $stmt = $conexion->prepare("INSERT INTO pedidos (usuario_id, total, metodo_pago, direccion, telefono) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $total, "paypal", $direccion, $telefono]);
    $pedido_id = $conexion->lastInsertId();

    // Insertar detalle y descontar stock
    $stmt_detalle = $conexion->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    $stmt_stock = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
    foreach ($productos as $p) {
        $stmt_detalle->execute([$pedido_id, $p["id"], $p["cantidad"], $p["precio"]]);
        $stmt_stock->execute([$p["cantidad"], $p["id"]]);
    }

    $conexion->commit();
    echo json_encode(["success" => true, "mensaje" => "✅ Pedido registrado correctamente."]);
} catch (Exception $e) {
    $conexion->rollBack();
    echo json_encode(["success" => false, "mensaje" => "❌ Error al guardar el pedido: " . $e->getMessage()]);
}
?>
