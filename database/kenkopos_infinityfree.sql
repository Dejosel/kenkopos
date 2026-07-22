-- KenkoPOS - Script de instalación para InfinityFree
-- Importar este archivo estando dentro de la base de datos if0_42272128_kenkopos en phpMyAdmin

-- Creación de la tabla de productos
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserción de datos de prueba (Comidas Rápidas)
INSERT INTO products (name, sku, price) VALUES 
('Hamburguesa Clásica', 'HAM-001', 12000.00),
('Hamburguesa Doble Carne', 'HAM-002', 16500.00),
('Perro Caliente Especial', 'PER-001', 9500.00),
('Pizza Personal Pepperoni', 'PIZ-001', 18000.00),
('Papas Fritas Grandes', 'PAP-001', 6000.00),
('Alitas BBQ x6', 'ALI-001', 14000.00),
('Gaseosa 500ml', 'GAS-001', 3500.00),
('Jugo Natural', 'JUG-001', 5000.00);
