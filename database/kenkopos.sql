-- Creación de la base de datos (Opcional, si no existe)
CREATE DATABASE IF NOT EXISTS kenkopos_db;
USE kenkopos_db;

-- Creación de la tabla de productos
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserción de datos de prueba
INSERT INTO products (name, sku, price) VALUES 
('Acetaminofen 500mg', 'MED-001', 2500.00),
('Ibuprofeno 400mg', 'MED-002', 3200.50),
('Vitamina C', 'MED-003', 5000.00);
