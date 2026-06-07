# Migración a Framework Laravel

Este documento resume los aspectos clave implementados durante la migración del módulo a Laravel:

## 1. Migraciones (Esquema de BD)
Se utilizó el sistema de migraciones de Laravel para recrear la estructura de la base de datos MySQL original.
- Archivo: `database/migrations/*_create_products_table.php`
- Retiene los mismos campos: `product_id`, `name`, `sku`, `price`.

## 2. Eloquent ORM
Se reemplazó la clase PDO nativa y las sentencias SQL preparadas manuales por Eloquent.
- **Antes:** `$stmt = $this->conn->prepare("INSERT INTO products...");`
- **Ahora:** `Product::create($validated);`

## 3. Validaciones
Las validaciones ahora están integradas en el Request del controlador.
- Se agregó validación para que el SKU sea único en la base de datos (`unique:products,sku`).

## 4. Vistas Blade
- Se unificó el código HTML redundante usando layouts.
- `@extends('layouts.app')`
- Se agregó directiva `@csrf` para seguridad de formularios.
