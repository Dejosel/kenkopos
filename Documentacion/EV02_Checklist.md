# Checklist de Requisitos - GA7-220501096-AA2-EV02
## KenkoPOS - Módulos Codificados y Probados

**Versión:** v1.1.0  
**Fecha:** Junio 2026

---

## Verificación de Requisitos

| # | Requisito solicitado | Evidencia en KenkoPOS | Archivo de referencia | Estado |
|---|---|---|---|---|
| 1 | Formularios HTML implementados | Dos formularios HTML: crear producto y editar producto | `public/products/create.php` L31, `public/products/edit.php` L45 | ✅ |
| 2 | Uso del método GET | `$_GET['id']` para consulta por ID; `$_GET['msg']` y `$_GET['error']` para retroalimentación al usuario | `edit.php` L12, `list.php` L24, `create.php` L27 | ✅ |
| 3 | Uso del método POST | Formularios de Crear, Editar y Eliminar usan `method="POST"` con verificación `$_SERVER["REQUEST_METHOD"]` | `create.php` L5, `edit.php` L7, `delete.php` L4 | ✅ |
| 4 | Conexión a MySQL | Clase `Database` con PDO, charset UTF-8, modo excepción y fetch asociativo | `config/database.php` | ✅ |
| 5 | Módulos codificados | Módulo CRUD completo: Listar, Crear, Editar, Eliminar, Consultar por ID | `app/Controllers/ProductController.php`, `app/Models/Product.php` | ✅ |
| 6 | Módulos probados | 20 casos de prueba funcional documentados en `EV02_Pruebas.md` | `Documentacion/EV02_Pruebas.md` | ✅ |
| 7 | Uso de Git | Repositorio inicializado, commits realizados, tags de versión v1.0.0 y v1.1.0 | https://github.com/Dejosel/kenkopos | ✅ |
| 8 | Prepared Statements | `bindParam()` en todas las consultas INSERT, UPDATE, DELETE, SELECT | `app/Models/Product.php` L56, L77-79, L99-102, L117 | ✅ |
| 9 | Programación Orientada a Objetos | 4 clases: `Database`, `Product`, `ProductController`, `Response` con atributos y métodos | `app/` y `config/` | ✅ |
| 10 | Validaciones básicas | Verificación de campos vacíos (`!empty()`) antes de ejecutar operaciones en BD | `app/Controllers/ProductController.php` L48, L68 | ✅ |
| 11 | Manejo de errores | `try/catch PDOException` en conexión; redirecciones con parámetros `?error=` en controlador | `config/database.php` L35-36, `ProductController.php` | ✅ |
| 12 | Relación con Historias de Usuario | HU-01 a HU-04 cubren exactamente las 4 operaciones CRUD implementadas | `docs/historias_usuario.md` | ✅ |
| 13 | Relación con Casos de Uso | CU: Crear, Listar, Modificar y Eliminar producto documentados y coinciden con el código | `docs/casos_uso.md` | ✅ |
| 14 | Relación con Diagrama de Clases | Diagrama refleja `Database`, `Product`, `ProductController`, `Response` y sus relaciones | `docs/diagrama_clases.md` | ✅ |
| 15 | Versionamiento semántico | v1.0.0 (entrega EV01) → v1.1.0 (entrega EV02 con documentación ampliada) | Tags en repositorio GitHub | ✅ |

---

## Resumen

| Categoría | Total | Cumplidos |
|---|---|---|
| Requisitos técnicos | 11 | 11 ✅ |
| Requisitos documentales | 4 | 4 ✅ |
| **TOTAL** | **15** | **15 ✅** |
