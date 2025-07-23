<?php
session_start();
$usuario_id = $_SESSION["usuario_id"] ?? null;

if (!$usuario_id) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi carrito</title>
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/carrito.css">
</head>
<body>
  <?php include("partials/header.php"); ?>

  <h1>🛒 Carrito de compras</h1>
  <table border="1" cellpadding="10">
    <thead>
      <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
        <th>Eliminar</th>
      </tr>
    </thead>
    <tbody id="carrito-body"></tbody>
  </table>

  <h2>Total: S/ <span id="total">0.00</span></h2>

  <h2>Pagar con PayPal</h2>
  <div id="paypal-button-container"></div>

  <h3>📍 Dirección de entrega y contacto</h3>
  <form id="form-datos-contacto">
    <label>Dirección:</label><br>
    <input type="text" id="direccionPaypal" required><br><br>

    <label>Teléfono:</label><br>
    <input type="text" id="telefonoPaypal" required><br><br>
  </form>

  <script>
    const usuarioId = <?= json_encode($usuario_id) ?>;
    const carritoKey = "carrito_" + usuarioId;
    const carrito = JSON.parse(localStorage.getItem(carritoKey)) || [];
    const totalPaypal = carrito.reduce((acc, p) => acc + p.precio * p.cantidad, 0).toFixed(2);
  </script>

  <script src="https://www.paypal.com/sdk/js?client-id=Ab5IooG3DJ7sPqVJeCH8Due9kk-_7IP77yFtvAQIwG8f7dsxDIGVY3LypDFrrovjWODig_RRxZr-maFc&currency=USD"></script>
  <script>
    paypal.Buttons({
      createOrder: function(data, actions) {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: totalPaypal
            }
          }]
        });
      },
      onApprove: function(data, actions) {
        const direccion = document.getElementById("direccionPaypal").value.trim();
        const telefono = document.getElementById("telefonoPaypal").value.trim();

        // Validar campos obligatorios
        if (!direccion || !telefono) {
          alert("❌ Por favor, completa la dirección y el teléfono antes de pagar.");
          if (!direccion) document.getElementById("direccionPaypal").style.border = "2px solid red";
          if (!telefono) document.getElementById("telefonoPaypal").style.border = "2px solid red";
          return;
        }

        return actions.order.capture().then(function(details) {
          alert("✅ Pago realizado por " + details.payer.name.given_name);

          fetch("../api/guardar_pedido.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              productos: carrito,
              total: totalPaypal,
              direccion: direccion,
              telefono: telefono
            })
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              alert("Pedido registrado correctamente.");
              localStorage.removeItem(carritoKey);
              localStorage.setItem("carritoResumen", JSON.stringify(carrito));
              localStorage.setItem("direccionResumen", direccion);
              localStorage.setItem("telefonoResumen", telefono);
              window.location.href = "gracias.php";
            } else {
              alert("❌ Error al guardar el pedido: " + data.mensaje);
            }
          });
        });
      }
    }).render('#paypal-button-container');
  </script>

  <h2>Pagar con Yape</h2>
  <button onclick="mostrarYape()">Mostrar QR</button>

  <div id="qrYape" style="display:none; text-align:center; margin-top:20px;">
    <p>Escanea este código QR con Yape y sube tu comprobante</p>
    <img src="img/qr-yape.png" width="200">
    <form action="../api/guardar_pedido.php" method="POST" enctype="multipart/form-data" onsubmit="return enviarConYape()">
      <input type="hidden" name="desdeYape" value="1">
      <input type="hidden" name="datosCarrito" id="datosCarrito">

      <br><label>📍 Dirección de entrega:</label><br>
      <textarea name="direccion" required placeholder="Av. Ejemplo 123, Distrito, Ciudad" rows="3" cols="40"></textarea><br><br>

      <label>📞 Teléfono de contacto:</label><br>
      <input type="text" name="telefono" required placeholder="912345678"><br><br>

      <label>📄 Comprobante de pago:</label><br>
      <input type="file" name="comprobante" required><br><br>

      <button type="submit">Enviar pedido con Yape</button>
    </form>
  </div>

  <script>
    function mostrarYape() {
      document.getElementById("qrYape").style.display = "block";
    }

    function enviarConYape() {
      const carrito = JSON.parse(localStorage.getItem(carritoKey)) || [];

      if (carrito.length === 0) {
        alert("❌ Tu carrito está vacío. Agrega productos antes de enviar tu pedido.");
        return false;
      }

      const direccion = document.querySelector("textarea[name='direccion']").value;
      const telefono = document.querySelector("input[name='telefono']").value;

      localStorage.setItem("carritoResumen", JSON.stringify(carrito));
      localStorage.setItem("direccionResumen", direccion);
      localStorage.setItem("telefonoResumen", telefono);

      document.getElementById("datosCarrito").value = JSON.stringify(carrito);
      return true;
    }
  </script>

  <script>
    const cuerpo = document.getElementById("carrito-body");
    const total = document.getElementById("total");
    let suma = 0;

    carrito.forEach((producto, index) => {
      const subtotal = producto.precio * producto.cantidad;
      suma += subtotal;

      const fila = document.createElement("tr");
      fila.innerHTML = `
        <td>${producto.nombre}</td>
        <td>S/ ${producto.precio.toFixed(2)}</td>
        <td>
          <input type="number" min="1" value="${producto.cantidad}" onchange="actualizarCantidad(${index}, this.value)">
        </td>
        <td>S/ ${subtotal.toFixed(2)}</td>
        <td><button onclick="eliminar(${index})">❌</button></td>
      `;
      cuerpo.appendChild(fila);
    });

    total.textContent = suma.toFixed(2);

    function eliminar(i) {
      carrito.splice(i, 1);
      localStorage.setItem(carritoKey, JSON.stringify(carrito));
      location.reload();
    }

    function actualizarCantidad(index, nuevaCantidad) {
      nuevaCantidad = parseInt(nuevaCantidad);
      if (nuevaCantidad < 1) nuevaCantidad = 1;

      carrito[index].cantidad = nuevaCantidad;
      localStorage.setItem(carritoKey, JSON.stringify(carrito));
      location.reload();
    }
  </script>
</body>
</html>
