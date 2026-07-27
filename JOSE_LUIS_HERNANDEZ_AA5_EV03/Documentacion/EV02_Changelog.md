# Changelog - KenkoPOS

Todos los cambios notables de este proyecto están documentados en este archivo.
El formato está basado en [Keep a Changelog](https://keepachangelog.com/es/1.0.0/).

---

## [v1.1.0] - 2026-06-04

### Añadido
- `Documentacion/EV02_Informe.md` — Informe técnico completo para la evidencia GA7-220501096-AA2-EV02.
- `Documentacion/EV02_Pruebas.md` — Tabla con 20 casos de prueba funcional para todos los módulos CRUD.
- `Documentacion/EV02_Checklist.md` — Tabla de verificación de 15 requisitos de la evidencia EV02.
- `Documentacion/EV02_Changelog.md` — Este archivo de registro de cambios.
- `Documentacion/Repositorio.txt` — Información de acceso al repositorio del proyecto.

### Sin cambios en el código fuente
- No se realizaron modificaciones al código PHP (v1.0.0 ya cumple todos los requisitos técnicos de EV02).
- La estructura de archivos PHP, SQL, CSS y JS permanece idéntica a v1.0.0.

---

## [v1.0.0] - 2026-06-01

### Añadido (entrega inicial - EV01)
- Estructura base del proyecto KenkoPOS con arquitectura MVC simplificada.
- `config/database.php` — Clase `Database` con conexión PDO a MySQL.
- `app/Models/Product.php` — Clase `Product` con métodos CRUD: `readAll()`, `readById()`, `create()`, `update()`, `delete()`.
- `app/Controllers/ProductController.php` — Clase `ProductController` con métodos: `index()`, `show()`, `store()`, `update()`, `destroy()`.
- `app/Helpers/Response.php` — Helper `Response` con método estático `redirect()`.
- `public/index.php` — Punto de entrada con redirección al listado de productos.
- `public/products/list.php` — Vista de listado de productos.
- `public/products/create.php` — Formulario y procesamiento de creación de producto.
- `public/products/edit.php` — Formulario y procesamiento de edición de producto.
- `public/products/delete.php` — Procesamiento de eliminación de producto.
- `public/assets/css/style.css` — Estilos personalizados.
- `public/assets/js/app.js` — Confirmación de eliminación via JavaScript.
- `database/kenkopos.sql` — Script SQL con creación de BD, tabla y datos de prueba.
- `docs/historias_usuario.md` — 4 historias de usuario (HU-01 a HU-04).
- `docs/casos_uso.md` — 4 casos de uso: Crear, Listar, Modificar, Eliminar.
- `docs/diagrama_clases.md` — Diagrama de clases en texto plano.
- `docs/informe_tecnico.md` — Informe técnico de la evidencia EV01.
- `README.md` — Documentación de instalación y uso del proyecto.
- `.gitignore` — Exclusión de archivos de entorno y dependencias.
