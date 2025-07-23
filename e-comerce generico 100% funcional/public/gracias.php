<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gracias por tu compra</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #fff;
      margin: 0;
      padding: 0;
      color: #333;
    }

    .mensaje {
      width: 210mm;
      height: 297mm;
      padding: 15mm 10mm;
      margin: auto;
      background: #fff;
      overflow: hidden;
      position: relative;
    }

    .encabezado {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .encabezado img {
      height: 60px;
    }

    .empresa-info {
      text-align: right;
    }

    .empresa-info h2 {
      margin: 0;
      font-weight: 600;
    }

    hr {
      margin: 15px 0;
      border: none;
      border-top: 2px solid #ccc;
    }

    h1 {
      text-align: center;
      color: #28a745;
      margin: 10px 0;
      font-size: 20px;
    }

    h3 {
      margin: 12px 0 5px;
      color: #555;
      font-size: 14px;
    }

    p {
      margin: 0;
      font-size: 13px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 13px;
    }

    th {
      background-color: #28a745;
      color: white;
      padding: 6px;
      text-align: left;
    }

    td {
      padding: 6px;
      border-bottom: 1px solid #ddd;
    }

    .total {
      text-align: right;
      font-size: 14px;
      font-weight: 600;
      margin-top: 10px;
    }

    .nota-legal {
      font-size: 11px;
      color: #777;
      margin-top: 15px;
      text-align: justify;
    }

    .qr-container {
      text-align: center;
      margin-top: 10px;
    }

    .qr-container canvas {
      display: block;
      margin: auto;
      max-width: 70px;
      max-height: 70px;
    }

    @media print {
      .solo-web {
        display: none !important;
      }
    }

    .solo-web {
      text-align: center;
      margin-top: 15px;
    }

    .boton {
      display: inline-block;
      padding: 8px 16px;
      background-color: #007bff;
      color: white;
      border: none;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.3s;
      margin: 5px;
    }

    .boton:hover {
      background-color: #0056b3;
    }

    .pdf-btn {
      background-color: #ffc107;
      color: #333;
    }

    .pdf-btn:hover {
      background-color: #e0a800;
    }
  </style>
</head>
<body>

<div class="mensaje" id="comprobante">

  <!-- Encabezado -->
  <div class="encabezado">
    <img src="img/logo.png" alt="Logo de la empresa">
    <div class="empresa-info">
      <h2>Encanto Íntimo</h2>
      <p>RUC: 20481234567</p>
      <p>www.encanto-intimo.com</p>
    </div>
  </div>

  <hr>

  <h1>🎉 ¡Gracias por tu compra!</h1>
  <p style="text-align: center;">Tu pedido ha sido registrado correctamente.</p>

  <h3>📍 Dirección de envío:</h3>
  <p id="direccion">Cargando...</p>

  <h3>📞 Teléfono de contacto:</h3>
  <p id="telefono">Cargando...</p>

  <h3>🧾 Detalle del pedido:</h3>
  <table>
    <thead>
      <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody id="resumen-carrito"></tbody>
  </table>

  <p class="total">Total pagado: S/ <span id="total">0.00</span></p>

  <div class="qr-container">
    <p style="margin-bottom: 5px;">📱 Escanea para contactarnos por WhatsApp</p>
    <div  id="qrcode" style="display: flex; justify-content: center;"></div>
  </div>

  <p class="nota-legal">
    Este documento es una constancia de compra emitida por Encanto Íntimo. No reemplaza a una boleta o factura tributaria. Para solicitar comprobantes fiscales, contáctenos a través de nuestro canal de atención.
  </p>

  <!-- Botones (solo en web) -->
  <div class="solo-web">
    <button class="boton pdf-btn" onclick="descargarPDF()">📄 Descargar comprobante en PDF</button>
    <a href="index.php" class="boton">🏬 Volver a la tienda</a>
  </div>

</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
  const carrito = JSON.parse(localStorage.getItem("carritoResumen")) || [];
  const direccion = localStorage.getItem("direccionResumen") || "No disponible";
  const telefono = localStorage.getItem("telefonoResumen") || "No disponible";

  document.getElementById("direccion").textContent = direccion;
  document.getElementById("telefono").textContent = telefono;

  const cuerpo = document.getElementById("resumen-carrito");
  const totalSpan = document.getElementById("total");
  let total = 0;

  carrito.forEach(p => {
    const subtotal = p.precio * p.cantidad;
    total += subtotal;
    const fila = document.createElement("tr");
    fila.innerHTML = `
      <td>${p.nombre}</td>
      <td>S/ ${p.precio.toFixed(2)}</td>
      <td>${p.cantidad}</td>
      <td>S/ ${subtotal.toFixed(2)}</td>
    `;
    cuerpo.appendChild(fila);
  });

  totalSpan.textContent = total.toFixed(2);

  function descargarPDF() {
    document.querySelectorAll('.solo-web').forEach(el => el.style.display = 'none');

    const element = document.getElementById('comprobante');
    const opt = {
      margin:       0,
      filename:     'comprobante.pdf',
      image:        { type: 'png', quality: 1 },
      html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => {
      document.querySelectorAll('.solo-web').forEach(el => el.style.display = 'block');
    });
  }

  // QR WhatsApp
  const whatsappURL = "https://wa.me/51936335603?text=Hola,%20quiero%20consultar%20sobre%20mi%20pedido.";
  new QRCode(document.getElementById("qrcode"), {
    text: whatsappURL,
    width: 130,
    height: 130,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
  });
</script>

</body>
</html>
