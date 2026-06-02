# Casos de Uso - KenkoPOS (Módulo de Productos)

## Caso de Uso: Gestionar Productos (CRUD)
**Actor principal:** Administrador

### 1. Crear Producto
- **Flujo Principal:** 
  1. El Administrador accede a la opción "Nuevo Producto".
  2. El sistema muestra el formulario.
  3. El Administrador ingresa SKU, Nombre y Precio y hace clic en "Guardar".
  4. El sistema valida y guarda en base de datos.
  5. El sistema redirige al listado mostrando éxito.

### 2. Listar Productos
- **Flujo Principal:**
  1. El Administrador ingresa al módulo de productos.
  2. El sistema consulta la base de datos y renderiza una tabla con la información ordenada.

### 3. Modificar Producto
- **Flujo Principal:**
  1. El Administrador hace clic en "Editar" sobre un producto.
  2. El sistema recupera el producto por su ID y llena el formulario.
  3. El Administrador modifica un dato (Ej. precio) y da clic en "Actualizar".
  4. El sistema actualiza el registro mediante UPDATE.

### 4. Eliminar Producto
- **Flujo Principal:**
  1. El Administrador da clic en "Eliminar".
  2. El sistema muestra una confirmación.
  3. Al aceptar, el sistema envía una petición POST al controlador.
  4. El controlador ejecuta la sentencia DELETE y refresca la vista.
