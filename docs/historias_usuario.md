# Historias de Usuario - KenkoPOS (Módulo de Productos)

| ID  | Historia de Usuario | Criterios de Aceptación |
| --- | ------------------- | ----------------------- |
| HU-01 | Como administrador, quiero poder registrar un nuevo producto en el sistema especificando nombre, SKU y precio, para tenerlo disponible en el catálogo. | 1. El formulario debe requerir todos los campos obligatorios.<br>2. El SKU debe ser guardado correctamente.<br>3. Redirigir a la lista mostrando mensaje de éxito. |
| HU-02 | Como administrador, quiero visualizar el listado completo de productos registrados para conocer mi inventario. | 1. Mostrar tabla con ID, Nombre, SKU y Precio.<br>2. Formatear el precio con dos decimales.<br>3. Mostrar mensaje si no hay registros. |
| HU-03 | Como administrador, quiero poder editar la información de un producto existente para corregir errores de digitación o cambiar precios. | 1. Cargar datos actuales en un formulario.<br>2. Validar que no se envíen campos vacíos.<br>3. Guardar cambios en BD y redirigir. |
| HU-04 | Como administrador, quiero eliminar un producto del catálogo que ya no se comercializa para mantener mi inventario limpio. | 1. Mostrar alerta de confirmación (JavaScript) antes de eliminar.<br>2. Borrar permanentemente de la BD.<br>3. Redirigir al listado con mensaje de éxito. |
