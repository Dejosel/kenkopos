# Documentación de Endpoints - Módulo de Productos (KenkoPOS)

Esta sección documenta de forma exhaustiva los servicios web de la API REST para el módulo de **Productos** de la aplicación KenkoPOS. La API utiliza formato JSON para el intercambio de datos tanto en la solicitud como en la respuesta, y se apoya en los códigos de estado HTTP estándar.

---

## Índice de Endpoints

1. [Obtener todos los productos (GET)](#1-obtener-todos-los-productos-get)
2. [Obtener un producto por ID (GET)](#2-obtener-un-producto-por-id-get)
3. [Crear producto (POST)](#3-crear-producto-post)
4. [Actualizar producto (PUT)](#4-actualizar-producto-put)
5. [Eliminar producto (DELETE)](#5-eliminar-producto-delete)

---

## 1. Obtener todos los productos (GET)

Obtiene la lista completa de productos registrados en el sistema.

* **Método HTTP:** `GET`
* **URL:** `/api/products.php`
* **Cabeceras obligatorias:**
  * `Accept: application/json`
* **Cuerpo de la Petición:** No aplica (Vacío).

### Respuesta Exitosa (HTTP 200 OK)
```json
{
    "success": true,
    "message": "Productos obtenidos correctamente",
    "products": [
        {
            "product_id": 1,
            "name": "Hamburguesa Especial",
            "sku": "PLT-001",
            "price": 22000,
            "category": "Platos Fuertes",
            "color": "#dc3545",
            "created_at": "2026-07-08 20:33:37"
        },
        {
            "product_id": 2,
            "name": "Pizza Margarita",
            "sku": "PLT-002",
            "price": 18000,
            "category": "Platos Fuertes",
            "color": "#dc3545",
            "created_at": "2026-07-08 20:33:37"
        }
    ]
}
```

### Posibles Códigos de Respuesta HTTP
* **`200 OK`**: Petición procesada correctamente. Retorna un arreglo en la propiedad `products`. Si la base de datos no tiene productos, el arreglo retornará vacío `[]` con código 200.

---

## 2. Obtener un producto por ID (GET)

Obtiene la información detallada de un único producto mediante su identificador numérico.

* **Método HTTP:** `GET`
* **URL:** `/api/products.php?id={id}`
* **Parámetros de la URL:**
  * `id` *(entero, obligatorio)*: Identificador único del producto en la base de datos (ej: `id=1`).
* **Cabeceras obligatorias:**
  * `Accept: application/json`
* **Cuerpo de la Petición:** No aplica (Vacío).

### Respuesta Exitosa (HTTP 200 OK)
```json
{
    "success": true,
    "message": "Producto encontrado",
    "product": {
        "product_id": 1,
        "name": "Hamburguesa Especial",
        "sku": "PLT-001",
        "price": 22000,
        "category": "Platos Fuertes",
        "color": "#dc3545",
        "created_at": "2026-07-08 20:33:37"
    }
}
```

### Respuestas de Error
#### Producto No Encontrado (HTTP 404 Not Found)
*Ocurre cuando el ID de producto provisto no existe en la base de datos.*
```json
{
    "success": false,
    "message": "Producto no encontrado"
}
```

### Posibles Códigos de Respuesta HTTP
* **`200 OK`**: El producto fue localizado con éxito y se incluye en la propiedad `product`.
* **`404 Not Found`**: El producto con el ID especificado no existe.

---

## 3. Crear producto (POST)

Crea y registra un nuevo producto en el catálogo del sistema POS.

* **Método HTTP:** `POST`
* **URL:** `/api/products.php`
* **Cabeceras obligatorias:**
  * `Content-Type: application/json`
* **Cuerpo de la Petición (JSON):**
```json
{
    "name": "Pizza Personal",
    "sku": "PZ001",
    "price": 22000,
    "category": "Comidas",
    "color": "Rojo"
}
```

### Parámetros del Body JSON:
* `name` *(string, obligatorio)*: Nombre del producto.
* `sku` *(string, obligatorio)*: Código único de inventario.
* `price` *(número, obligatorio)*: Precio unitario. Debe ser un número estrictamente mayor que cero (`> 0`).
* `category` *(string, obligatorio)*: Nombre de la categoría.
* `color` *(string, opcional)*: Código de color hexadecimal o nombre de color asociado al producto.

### Respuesta Exitosa (HTTP 201 Created)
```json
{
    "success": true,
    "message": "Producto creado correctamente"
}
```

### Respuestas de Error
#### Datos Incompletos (HTTP 400 Bad Request)
*Ocurre si alguno de los campos obligatorios (`name`, `sku`, `price`, `category`) no es enviado en la petición.*
```json
{
    "success": false,
    "message": "Datos incompletos. Nombre, SKU, precio y categoría son obligatorios."
}
```

#### Precio Inválido (HTTP 400 Bad Request)
*Ocurre si el precio es menor o igual a cero.*
```json
{
    "success": false,
    "message": "El precio debe ser mayor a 0."
}
```

### Posibles Códigos de Respuesta HTTP
* **`201 Created`**: El producto fue creado y guardado físicamente en la base de datos de manera exitosa.
* **`400 Bad Request`**: Datos incompletos o validaciones de formato/valores incorrectas.
* **`500 Internal Server Error`**: Ocurrió un error interno en el servidor o la base de datos al guardar la información.

---

## 4. Actualizar producto (PUT)

Actualiza los datos de un producto existente especificando su identificador.

* **Método HTTP:** `PUT`
* **URL:** `/api/products.php`
* **Cabeceras obligatorias:**
  * `Content-Type: application/json`
* **Cuerpo de la Petición (JSON):**
```json
{
    "product_id": 1,
    "name": "Pizza Especial",
    "sku": "PZ001",
    "price": 25000,
    "category": "Comidas",
    "color": "Negro"
}
```

*Nota: También es posible enviar el ID a través del parámetro en la URL query string `/api/products.php?id=1`.*

### Parámetros del Body JSON:
* `product_id` *(entero, obligatorio si no se envía por URL)*: ID del producto que se actualizará.
* `name` *(string, obligatorio)*: Nuevo nombre.
* `sku` *(string, obligatorio)*: Nuevo código único de SKU.
* `price` *(número, obligatorio)*: Nuevo precio. Debe ser mayor a 0.
* `category` *(string, obligatorio)*: Nueva categoría asignada.
* `color` *(string, opcional)*: Nuevo color asignado.

### Respuesta Exitosa (HTTP 200 OK)
```json
{
    "success": true,
    "message": "Producto actualizado correctamente"
}
```

### Respuestas de Error
#### ID no suministrado (HTTP 400 Bad Request)
```json
{
    "success": false,
    "message": "Se requiere el ID del producto a actualizar."
}
```

#### Datos incompletos (HTTP 400 Bad Request)
```json
{
    "success": false,
    "message": "Datos incompletos para actualizar."
}
```

#### Producto No Encontrado (HTTP 404 Not Found)
*Ocurre si el producto con el ID especificado no existe.*
```json
{
    "success": false,
    "message": "Producto no encontrado para actualizar."
}
```

### Posibles Códigos de Respuesta HTTP
* **`200 OK`**: El producto fue actualizado exitosamente.
* **`400 Bad Request`**: Datos incompletos o ID omitido.
* **`404 Not Found`**: El producto a actualizar no existe en el sistema.
* **`500 Internal Server Error`**: Ocurrió un error imprevisto al procesar la actualización en la base de datos.

---

## 5. Eliminar producto (DELETE)

Elimina permanentemente del catálogo un producto seleccionado por su identificador.

* **Método HTTP:** `DELETE`
* **URL:** `/api/products.php?id={id}`
* **Parámetros de la URL:**
  * `id` *(entero, obligatorio)*: El identificador único del producto que se desea eliminar.
* **Cabeceras obligatorias:**
  * `Accept: application/json`
* **Cuerpo de la Petición:** No aplica (Vacío).

### Respuesta Exitosa (HTTP 200 OK)
```json
{
    "success": true,
    "message": "Producto eliminado correctamente"
}
```

### Respuestas de Error
#### ID no suministrado (HTTP 400 Bad Request)
```json
{
    "success": false,
    "message": "Se requiere el ID del producto a eliminar."
}
```

#### Producto No Encontrado (HTTP 404 Not Found)
```json
{
    "success": false,
    "message": "Producto no encontrado para eliminar."
}
```

### Posibles Códigos de Respuesta HTTP
* **`200 OK`**: El producto fue eliminado físicamente de la base de datos con éxito.
* **`400 Bad Request`**: No se proporcionó el ID necesario en los parámetros.
* **`404 Not Found`**: El producto con el ID suministrado no existe en el catálogo.
* **`500 Internal Server Error`**: Ocurrió una falla durante la operación de eliminación en la base de datos.
