-- ============================================================
--  KenkoPOS — Script de instalación para InfinityFree (MySQL)
--  Importar este archivo en phpMyAdmin dentro de la BD:
--  if0_42272128_kenkopos
-- ============================================================

-- ── 1. Tabla de catálogo de roles ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `roles` (
  `id`          TINYINT(1)   NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(20)  NOT NULL UNIQUE,
  `description` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `roles` (`name`, `description`) VALUES
  ('admin',   'Administrador: acceso total al sistema, gestión de usuarios y productos'),
  ('cajero',  'Cajero: puede procesar y visualizar órdenes, consultar productos'),
  ('mesero',  'Mesero: solo puede consultar el catálogo de productos');

-- ── 2. Tabla de usuarios con rol ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('admin','cajero','mesero') NOT NULL DEFAULT 'mesero',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ── 3. Tabla de productos ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(255) NOT NULL,
  `sku`        VARCHAR(50)  NOT NULL UNIQUE,
  `price`      DECIMAL(10,2) NOT NULL,
  `category`   VARCHAR(100) NOT NULL DEFAULT 'General',
  `color`      VARCHAR(20)  DEFAULT NULL,
  `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de prueba — Comidas Rápidas
INSERT IGNORE INTO `products` (`name`, `sku`, `price`, `category`, `color`) VALUES
  ('Hamburguesa Clásica',        'HAM-001', 12000.00, 'Hamburguesas', '#dc3545'),
  ('Hamburguesa Doble Carne',    'HAM-002', 16500.00, 'Hamburguesas', '#dc3545'),
  ('Perro Caliente Especial',    'PER-001',  9500.00, 'Perros',       '#fd7e14'),
  ('Pizza Personal Pepperoni',   'PIZ-001', 18000.00, 'Pizzas',       '#ffc107'),
  ('Papas Fritas Grandes',       'PAP-001',  6000.00, 'Acompañantes', '#198754'),
  ('Alitas BBQ x6',              'ALI-001', 14000.00, 'Alitas',       '#6f42c1'),
  ('Gaseosa 500ml',              'GAS-001',  3500.00, 'Bebidas',      '#0dcaf0'),
  ('Jugo Natural',               'JUG-001',  5000.00, 'Bebidas',      '#20c997');
