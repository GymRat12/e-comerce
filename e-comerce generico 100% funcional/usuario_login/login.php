<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #e0f7fa, #ffffff);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background-color: #ffffff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
      animation: fadeIn 0.6s ease-in-out;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #00796b;
    }

    label {
      font-weight: bold;
      color: #444;
    }

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

    input[type="email"]:focus,
    input[type="password"]:focus {
      border-color: #00796b;
      outline: none;
    }

    button {
      width: 100%;
      background-color: #00796b;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #004d40;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: scale(0.95);}
      to {opacity: 1; transform: scale(1);}
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>🔐 Iniciar sesión</h2>
    <form action="procesar_login.php" method="POST">
      <label>Correo:</label><br>
      <input type="email" name="correo" required><br>

      <label>Contraseña:</label><br>
      <input type="password" name="contraseña" required><br>

      <button type="submit">Ingresar</button>
    </form>
  </div>
</body>
</html>
