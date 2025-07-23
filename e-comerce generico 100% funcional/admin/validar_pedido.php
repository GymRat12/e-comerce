<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<?php
require_once("../db/conexion.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conexion->prepare("UPDATE pedidos SET estado = 'pagado' WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo "<script>alert('✅ Pedido validado correctamente'); window.location.href='ver_pedidos.php';</script>";
    } else {
        echo "<script>alert('❌ Error al validar el pedido'); window.location.href='ver_pedidos.php';</script>";
    }
}
?>
