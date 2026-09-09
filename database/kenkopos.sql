-- ============================================================
--  KenkoPOS — Esquema de base de datos actualizado
--  Incluye: roles, permisos y columna role en users
-- ============================================================

-- ── 1. Tabla de catálogo de roles ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `roles` (
  `id`          TINYINT(1)   NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(20)  NOT NULL UNIQUE,
  `description` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar los roles del sistema (INSERT IGNORE evita duplicados)
INSERT IGNORE INTO `roles` (`name`, `description`) VALUES
  ('admin',   'Administrador: acceso total al sistema, gestión de usuarios y productos'),
  ('cajero',  'Cajero: puede procesar y visualizar órdenes, consultar productos'),
  ('mesero',  'Mesero: solo puede consultar el catálogo de productos');

-- ── 2. Tabla de usuarios ──────────────────────────────────────────────────────
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

-- ── MIGRACIÓN (si la tabla users ya existe sin columna role) ──────────────────
-- Ejecutar solo si es necesario (el sistema crea la BD desde cero normalmente):
--
-- ALTER TABLE `users`
--   ADD COLUMN `role` ENUM('admin','cajero','mesero') NOT NULL DEFAULT 'mesero'
--   AFTER `password`;
-- UPDATE `users` SET `role` = 'mesero' WHERE `role` IS NULL OR `role` = '';
