# Identificación de Módulos (Laravel)

El sistema KenkoPOS ha sido refactorizado en un entorno de framework, pero mantiene el módulo central:

## Módulo de Productos (CRUD)
Gestionado por `App\Http\Controllers\ProductController`.

| Acción | Método HTTP | Ruta de Laravel | Vista (Blade) |
|---|---|---|---|
| Listar | GET | `/products` | `products.index` |
| Formulario Crear | GET | `/products/create` | `products.create` |
| Guardar Nuevo | POST | `/products` | (Redirección) |
| Formulario Editar| GET | `/products/{id}/edit` | `products.edit` |
| Actualizar | PUT/PATCH | `/products/{id}` | (Redirección) |
| Eliminar | DELETE | `/products/{id}` | (Redirección) |

Este enrutamiento de Laravel reemplaza el acceso directo a los archivos `.php` que existía en la versión v1.x.
