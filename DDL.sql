-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS alumni CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE alumni;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','cliente') DEFAULT 'cliente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    imagen VARCHAR(255),
    descripcion TEXT,
    PRIMARY KEY (id)
);

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    precio_oferta DECIMAL(10,2) DEFAULT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria_id INT NOT NULL,
    imagen_principal VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

-- Tabla de imágenes adicionales de productos
CREATE TABLE IF NOT EXISTS producto_imagenes (
    id INT NOT NULL AUTO_INCREMENT,
    producto_id INT NOT NULL,
    imagen_url VARCHAR(255) NOT NULL,
    orden INT DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Tabla de atributos de productos (tallas, colores, etc.)
CREATE TABLE IF NOT EXISTS producto_atributos (
    id INT NOT NULL AUTO_INCREMENT,
    producto_id INT NOT NULL,
    atributo VARCHAR(50) NOT NULL, 
    valor VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Tabla de carrito de compras
CREATE TABLE IF NOT EXISTS carrito (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT,
    sesion_id VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de ítems del carrito
CREATE TABLE IF NOT EXISTS carrito_items (
    id INT NOT NULL AUTO_INCREMENT,
    carrito_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    atributos TEXT, 
    PRIMARY KEY (id),
    FOREIGN KEY (carrito_id) REFERENCES carrito(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Tabla de órdenes
CREATE TABLE IF NOT EXISTS ordenes (
    id INT NOT NULL AUTO_INCREMENT,
    usuario_id INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente','procesando','completada','cancelada') DEFAULT 'pendiente',
    total DECIMAL(10,2) NOT NULL,
    direccion_envio TEXT NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Tabla de ítems de órdenes
CREATE TABLE IF NOT EXISTS orden_items (
    id INT NOT NULL AUTO_INCREMENT,
    orden_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    atributos TEXT,
    PRIMARY KEY (id),
    FOREIGN KEY (orden_id) REFERENCES ordenes(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Tabla de reseñas
CREATE TABLE IF NOT EXISTS reseñas (
    id INT NOT NULL AUTO_INCREMENT,
    producto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    calificacion TINYINT NOT NULL,
    comentario TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de cupones
CREATE TABLE cupones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    descuento DECIMAL(10,2) NOT NULL,
    tipo ENUM('porcentaje', 'fijo') NOT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    usos_maximos INT DEFAULT NULL,
    usos_actuales INT DEFAULT 0
);

-- Datos iniciales
INSERT INTO categorias (nombre, descripcion) VALUES 
('Ropa', 'Todo tipo de prendas de vestir'),
('Electrónicos', 'Dispositivos electrónicos y gadgets'),
('Hogar', 'Artículos para el hogar'),
('Deportes', 'Equipamiento deportivo');

-- Usuario admin por defecto (password: admin123)
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Administrador', 'admin@tienda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');