document.addEventListener("DOMContentLoaded", () => {
  if (!usuarioId) {
    alert("⚠️ Debes iniciar sesión para agregar productos al carrito.");
    return;
  }

  const botones = document.querySelectorAll(".btn-agregar");

  botones.forEach((boton) => {
    boton.addEventListener("click", () => {
      const id = boton.dataset.id;
      const nombre = boton.dataset.nombre;
      const precio = parseFloat(boton.dataset.precio);

      let carritoKey = `carrito_${usuarioId}`;
      let carrito = JSON.parse(localStorage.getItem(carritoKey)) || [];

      // Verificar si el producto ya está en el carrito
      const productoExistente = carrito.find((p) => p.id === id);
      if (productoExistente) {
        productoExistente.cantidad += 1;
      } else {
        carrito.push({ id, nombre, precio, cantidad: 1 });
      }

      localStorage.setItem(carritoKey, JSON.stringify(carrito));
      alert("✅ Producto agregado al carrito");
    });
  });
});
