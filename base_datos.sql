CREATE DATABASE IF NOT EXISTS sistema_ventas DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_ventas;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES roles(id) ON DELETE RESTRICT
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    imagen_url VARCHAR(255),
    estado TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    estado_pago VARCHAR(50) DEFAULT 'pendiente',
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE detalles_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE RESTRICT
);

CREATE TABLE estadisticas_desempeno (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    tiempo_segundos DECIMAL(10, 2) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id) ON DELETE CASCADE
);

    CREATE TABLE categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL
    );

-- ==============================================================================
-- DATOS DE EJEMPLO PARA SISTEMA DE VENTAS Y ESTADÍSTICAS
-- ==============================================================================

-- 1. ROLES DEL SISTEMA
INSERT INTO roles (id, nombre) VALUES 
(1, 'administrador'), 
(2, 'cliente');

-- 2. USUARIOS DE PRUEBA
-- Nota: La contraseña para todos los usuarios es 'admin123' o 'cliente123'
INSERT INTO usuarios (id, id_rol, nombre, correo, contrasena) VALUES 
(1, 1, 'Administrador General', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- admin123
(2, 2, 'Mauricio P.', 'mauricio@ejemplo.com', '$2y$10$w8uH3qXfA2K7qB7M2c3W/u.I.8b1.jJqGj6O2vU/kO5b7qB7M2c3W'), -- cliente123
(3, 2, 'Ana Vargas', 'ana@ejemplo.com', '$2y$10$w8uH3qXfA2K7qB7M2c3W/u.I.8b1.jJqGj6O2vU/kO5b7qB7M2c3W'), -- cliente123
(4, 2, 'Carlos Mendoza', 'carlos@ejemplo.com', '$2y$10$w8uH3qXfA2K7qB7M2c3W/u.I.8b1.jJqGj6O2vU/kO5b7qB7M2c3W'); -- cliente123

-- 3. CATEGORÍAS COMERCIALES
INSERT INTO categorias (id, nombre) VALUES 
(1, 'Computación y Desarrollo'), 
(2, 'Accesorios y Periféricos'), 
(3, 'Juegos y Servicios Digitales'), 
(4, 'Automotriz y Tuning');

-- 4. CATÁLOGO DE PRODUCTOS (Precios en Bolivianos - Bs.)
INSERT INTO productos (id, id_categoria, nombre, descripcion, precio, stock, estado) VALUES 
(1, 1, 'Laptop HP EliteBook', 'Equipo optimizado para programar sistemas de gestión y bases de datos SQLite.', 6612.00, 15, 1),
(2, 1, 'Manual OSINT Avanzado', 'Técnicas de recolección de información y redes Tor.', 240.50, 30, 1),
(3, 2, 'Teclado Mecánico Custom', 'Switches intercambiables y respuesta táctil rápida.', 520.00, 25, 1),
(4, 2, 'Monitor Gamer 27"', 'Pantalla de alta tasa de refresco ideal para simulaciones.', 1350.00, 10, 1),
(5, 3, 'Pack 5000 Diamantes MLBB', 'Recarga virtual directa de diamantes para Mobile Legends.', 522.00, 100, 1),
(6, 3, 'Host Server Privado Aternos Pro', 'Alojamiento mensual optimizado para Modpack Heavenly Hunger.', 104.50, 50, 1),
(7, 3, 'Álbum Digital: Alma Dinamita', 'Descarga en alta fidelidad (FLAC) con pistas exclusivas.', 35.00, 200, 1),
(8, 3, 'Suscripción PedidosYa Plus', 'Membresía semestral para entregas prioritarias.', 175.00, 80, 1),
(9, 4, 'Alerón Fibra de Carbono 2G', 'Diseño aerodinámico específico para Mitsubishi Eclipse 2G.', 1740.00, 5, 1),
(10, 4, 'Pintura Verde Kawasaki Premium', 'Galón de pintura automotriz acrílica de alto brillo.', 590.00, 12, 1);

-- 5. SIMULACIÓN DE PEDIDOS (Para dar contexto visual)
INSERT INTO pedidos (id, id_usuario, total, metodo_pago, estado_pago, fecha_pedido) VALUES 
(1, 2, 6612.00, 'qr', 'pagado', '2026-06-10 10:30:00'),
(2, 3, 522.00, 'tarjeta', 'pagado', '2026-06-11 14:15:00'),
(3, 4, 1740.00, 'qr', 'pagado', '2026-06-12 09:45:00');

-- 6. DETALLES DE LOS PEDIDOS
INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES 
(1, 1, 1, 6612.00),
(2, 5, 1, 522.00),
(3, 9, 1, 1740.00);

-- ==============================================================================
-- 7. GENERACIÓN DE MUESTRA ESTADÍSTICA (Min 30 observaciones para Inferencia)
-- Datos simulados de tiempos de respuesta en segundos para el Dashboard
-- ==============================================================================

-- Generamos 32 registros de pedidos "fantasmas" y sus tiempos asociados
-- para que el cálculo de la Media, Varianza y Prueba de Hipótesis (Z) 
-- funcione automáticamente al instalar el sistema.

INSERT INTO pedidos (id, id_usuario, total, metodo_pago, estado_pago) VALUES 
(4, 2, 100, 'qr', 'pagado'), (5, 3, 150, 'tarjeta', 'pagado'), (6, 4, 200, 'qr', 'pagado'),
(7, 2, 100, 'qr', 'pagado'), (8, 3, 150, 'tarjeta', 'pagado'), (9, 4, 200, 'qr', 'pagado'),
(10, 2, 100, 'qr', 'pagado'), (11, 3, 150, 'tarjeta', 'pagado'), (12, 4, 200, 'qr', 'pagado'),
(13, 2, 100, 'qr', 'pagado'), (14, 3, 150, 'tarjeta', 'pagado'), (15, 4, 200, 'qr', 'pagado'),
(16, 2, 100, 'qr', 'pagado'), (17, 3, 150, 'tarjeta', 'pagado'), (18, 4, 200, 'qr', 'pagado'),
(19, 2, 100, 'qr', 'pagado'), (20, 3, 150, 'tarjeta', 'pagado'), (21, 4, 200, 'qr', 'pagado'),
(22, 2, 100, 'qr', 'pagado'), (23, 3, 150, 'tarjeta', 'pagado'), (24, 4, 200, 'qr', 'pagado'),
(25, 2, 100, 'qr', 'pagado'), (26, 3, 150, 'tarjeta', 'pagado'), (27, 4, 200, 'qr', 'pagado'),
(28, 2, 100, 'qr', 'pagado'), (29, 3, 150, 'tarjeta', 'pagado'), (30, 4, 200, 'qr', 'pagado'),
(31, 2, 100, 'qr', 'pagado'), (32, 3, 150, 'tarjeta', 'pagado'), (33, 4, 200, 'qr', 'pagado'),
(34, 2, 100, 'qr', 'pagado'), (35, 3, 150, 'tarjeta', 'pagado');

-- Insertamos los tiempos de ejecución de esos 35 pedidos en total (los 3 iniciales + 32)
-- Tiempos oscilando entre 85 y 130 segundos para dar una distribución realista.
INSERT INTO estadisticas_desempeno (id_pedido, tiempo_segundos) VALUES 
(1, 115.5), (2, 98.2), (3, 122.1), (4, 105.4), (5, 110.0), (6, 95.8),
(7, 125.3), (8, 118.9), (9, 102.5), (10, 111.4), (11, 109.2), (12, 114.7),
(13, 99.1), (14, 128.5), (15, 107.6), (16, 112.3), (17, 120.8), (18, 104.2),
(19, 116.5), (20, 101.9), (21, 121.4), (22, 108.7), (23, 113.6), (24, 119.2),
(25, 97.4), (26, 126.8), (27, 106.1), (28, 111.9), (29, 124.5), (30, 103.8),
(31, 117.2), (32, 100.5), (33, 123.7), (34, 109.8), (35, 114.1);