-- Creación de la base de datos (Opcional, si no existe)
CREATE DATABASE IF NOT EXISTS kenkopos_db;
USE kenkopos_db;

-- Creación de la tabla de productos
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100) DEFAULT 'General',
    color VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserción de datos de prueba (Farmacia y Restaurante al estilo SambaPOS)
INSERT INTO products (name, sku, price, category, color) VALUES 
-- Productos de Farmacia originales
('Acetaminofen 500mg', 'MED-001', 2500.00, 'Farmacia', '#0d6efd'),
('Ibuprofeno 400mg', 'MED-002', 3200.50, 'Farmacia', '#0d6efd'),
('Vitamina C', 'MED-003', 5000.00, 'Farmacia', '#0d6efd'),
-- Productos de Restaurante
('Hamburguesa Especial', 'PLT-001', 22000.00, 'Platos Fuertes', '#dc3545'),
('Pizza Margarita', 'PLT-002', 18000.00, 'Platos Fuertes', '#dc3545'),
('Perro Caliente Gigante', 'PLT-003', 14000.00, 'Platos Fuertes', '#dc3545'),
('Costillas BBQ', 'PLT-004', 28000.00, 'Platos Fuertes', '#dc3545'),
('Papas Fritas', 'ENT-001', 6500.00, 'Entradas', '#ffc107'),
('Empanadas de la Casa (3 und)', 'ENT-002', 8000.00, 'Entradas', '#ffc107'),
('Aros de Cebolla', 'ENT-003', 7000.00, 'Entradas', '#ffc107'),
('Limonada Natural', 'BEB-001', 5000.00, 'Bebidas', '#198754'),
('Gaseosa Coca-Cola', 'BEB-002', 4000.00, 'Bebidas', '#198754'),
('Cerveza Club Colombia', 'BEB-003', 7000.00, 'Bebidas', '#198754'),
('Torta de Tres Leches', 'POS-001', 9000.00, 'Postres', '#6f42c1'),
('Volcán de Chocolate', 'POS-002', 11000.00, 'Postres', '#6f42c1');
