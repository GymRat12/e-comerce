CREATE DATABASE ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ecommerce;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  correo VARCHAR(100) UNIQUE,
  contraseña VARCHAR(255)
);
ALTER TABLE usuarios ADD COLUMN dni VARCHAR(100) AFTER correo;
ALTER TABLE usuarios ADD COLUMN activo BOOLEAN DEFAULT 1;
ALTER TABLE usuarios ADD telefono VARCHAR(20) NULL;

select*from usuarios;

CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  descripcion TEXT,
  precio DECIMAL(10,2),
  imagen VARCHAR(255),
  stock INT
);
ALTER TABLE productos ADD COLUMN activo BOOLEAN DEFAULT 1;


DROP TABLE pedidos;
CREATE TABLE pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,               -- más adelante usaremos esto al conectar login
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(10,2),
  estado VARCHAR(50) DEFAULT 'pendiente',
  metodo_pago VARCHAR(20) -- 'paypal' o 'yape'
);
ALTER TABLE pedidos ADD COLUMN comprobante VARCHAR(255) NULL;
ALTER TABLE pedidos ADD COLUMN direccion TEXT, ADD COLUMN telefono VARCHAR(20);



DROP TABLE detalle_pedido;
CREATE TABLE detalle_pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT,
  producto_id INT,
  cantidad INT,
  precio_unitario DECIMAL(10,2),
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

DROP TABLE carrito;
CREATE TABLE carritos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE carrito_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  carrito_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL,
  FOREIGN KEY (carrito_id) REFERENCES carritos(id) ON DELETE CASCADE,
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);


CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  correo VARCHAR(100) UNIQUE,
  contrasena VARCHAR(255)
);

-- Agrega un admin por defecto (contraseña: admin123)
INSERT INTO admins (nombre, correo, contrasena) VALUES 
('Administrador', 'admin@tuweb.com', SHA2('admin123', 256));


SELECT * FROM productos;
SELECT * FROM pedidos;
SELECT * FROM detalle_pedido;
