<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de usuario</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #fff3e0, #ffffff);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .register-container {
      background-color: #ffffff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      max-width: 420px;
      width: 100%;
      animation: fadeIn 0.6s ease-in-out;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #d84315;
    }

    label {
      font-weight: bold;
      color: #444;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-top: 6px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
      transition: border 0.3s ease;
    }

    input:focus {
      border-color: #d84315;
      outline: none;
    }

    button {
      width: 100%;
      background-color: #d84315;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #bf360c;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: scale(0.95);}
      to {opacity: 1; transform: scale(1);}
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>📝 Crear cuenta</h2>
    <form action="procesar_registro.php" method="POST">
      <label>Nombre:</label><br>
      <input type="text" name="nombre" required><br>

      <label>DNI:</label><br>
      <input type="text" name="dni" required pattern="\d{8}" title="Debe tener 8 dígitos"><br>

      <label>Correo:</label><br>
      <input type="email" name="correo" required><br>

      <label>Contraseña:</label><br>
      <input type="password" name="contraseña" required><br>

      <button type="submit">Registrarse</button>
    </form>
  </div>
</body>
</html>
