<!-- public/partials/header.php -->
<!-- <?php
session_start();
?> -->

<header class="main-header">
  <div class="logo">
    <a href="/index.php">🛍️ Mi Tienda</a>
  </div>

  <nav class="main-nav">
    <a href="/public/productos.php">Productos</a>
    <a href="/public/mis_pedidos.php">Mis Pedidos</a>
    <a href="/public/carrito.php">🛒 Carrito</a>

    <?php if (isset($_SESSION['usuario_nombre'])): ?>
      <span class="user-name">👤 <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
      <a href="/usuario_login/logout.php" class="logout">🚪 Cerrar sesión</a>
    <?php else: ?>
      <a href="/usuario_login/login.php">🔐 Iniciar sesión</a>
      <a href="/usuario_login/registro.php">📝 Registrarse</a>
    <?php endif; ?>
  </nav>
</header>
