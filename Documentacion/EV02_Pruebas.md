# Pruebas Funcionales - GA7-220501096-AA2-EV02
## KenkoPOS - Módulo de Gestión de Productos

**Versión probada:** v1.1.0  
**Fecha:** Junio 2026

---

## Tabla de Pruebas

| ID | Módulo | Funcionalidad | Datos de entrada | Resultado esperado | Estado |
|---|---|---|---|---|---|
| PR-01 | Listar Productos | Mostrar tabla con todos los productos registrados | Ninguno (GET) | Se renderiza tabla HTML con todos los productos de la BD en orden descendente por fecha | ✅ Aprobado |
| PR-02 | Listar Productos | Mostrar mensaje cuando no hay productos | BD vacía | Se muestra la fila: "No hay productos registrados." | ✅ Aprobado |
| PR-03 | Listar Productos | Mostrar alerta de éxito tras operación | `?msg=created` en URL | Se muestra alerta Bootstrap verde con texto "Operación realizada con éxito." | ✅ Aprobado |
| PR-04 | Crear Producto | Formulario carga correctamente | GET a `create.php` | Se muestra formulario con campos SKU, Nombre y Precio vacíos y botón "Guardar Producto" | ✅ Aprobado |
| PR-05 | Crear Producto | Guardar producto con datos válidos | POST: `name=Ibuprofeno`, `sku=MED-100`, `price=3500` | Producto insertado en BD y redirección a `list.php?msg=created` | ✅ Aprobado |
| PR-06 | Crear Producto | Validación de campos vacíos | POST: campos en blanco | Redirige a `create.php?error=missing_data` y muestra alerta de error | ✅ Aprobado |
| PR-07 | Crear Producto | Saneamiento de entrada XSS | POST: `name=<script>alert(1)</script>` | Se almacena texto plano sin ejecutar el script; `htmlspecialchars()` protege la salida | ✅ Aprobado |
| PR-08 | Editar Producto | Cargar formulario con datos existentes | GET: `edit.php?id=1` | Formulario pre-cargado con los datos actuales del producto ID=1 | ✅ Aprobado |
| PR-09 | Editar Producto | Redirigir si ID no existe | GET: `edit.php?id=9999` | Redirección a `list.php?error=not_found` | ✅ Aprobado |
| PR-10 | Editar Producto | Redirigir si no se proporciona ID | GET: `edit.php` (sin parámetro) | Redirección a `list.php` | ✅ Aprobado |
| PR-11 | Editar Producto | Actualizar datos con valores válidos | POST: `product_id=1`, `name=NuevoNombre`, `sku=SKU-NEW`, `price=9000` | Registro actualizado en BD y redirección a `list.php?msg=updated` | ✅ Aprobado |
| PR-12 | Editar Producto | Validación de campos vacíos en actualización | POST: `product_id=1`, campos en blanco | Redirige a `edit.php?id=1&error=missing_data` | ✅ Aprobado |
| PR-13 | Eliminar Producto | Confirmación en cliente antes de eliminar | Clic en botón "Eliminar" | Se muestra `confirm()` de JavaScript con mensaje de advertencia | ✅ Aprobado |
| PR-14 | Eliminar Producto | Cancelar eliminación | Clic "Cancelar" en confirm() | No se envía el formulario, no se borra nada | ✅ Aprobado |
| PR-15 | Eliminar Producto | Eliminar producto existente | POST: `product_id=1` a `delete.php` | Registro eliminado de la BD y redirección a `list.php?msg=deleted` | ✅ Aprobado |
| PR-16 | Eliminar Producto | Acceso directo por GET a delete.php | GET: `delete.php` (sin POST) | Redirección a `list.php` sin ejecutar ninguna operación | ✅ Aprobado |
| PR-17 | Conexión BD | Conectar a MySQL mediante PDO | Credenciales correctas en `config/database.php` | Se retorna objeto PDO activo sin excepciones | ✅ Aprobado |
| PR-18 | Conexión BD | Manejo de error de conexión | Credenciales incorrectas | Se muestra mensaje `die()`: "Error de conexión a la base de datos: ..." | ✅ Aprobado |
| PR-19 | Consultar por ID | Retornar datos de producto existente | `readById(1)` | Array asociativo con los datos del producto ID=1 | ✅ Aprobado |
| PR-20 | Consultar por ID | Retornar false si ID no existe | `readById(99999)` | Retorna `false`, controlador redirige con error | ✅ Aprobado |

---

## Notas de Prueba

- Las pruebas fueron realizadas de forma **manual funcional**, navegando la interfaz del proyecto en un servidor local PHP.
- Las pruebas de seguridad (PR-07, PR-16) verifican comportamientos implementados en el código sin necesidad de herramientas externas.
- El estado ✅ **Aprobado** indica que el comportamiento observado coincide con el resultado esperado.
