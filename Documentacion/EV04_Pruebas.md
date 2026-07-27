# Documento de Pruebas de la API – Módulo de Productos
## Evidencia GA7-220501096-AA5-EV04 – API del Proyecto (KenkoPOS)

---

## PORTADA

|  |  |
|---|---|
| **Título** | Pruebas de API REST – Módulo de Productos |
| **Evidencia** | GA7-220501096-AA5-EV04 – API del Proyecto |
| **Proyecto** | KenkoPOS (Sistema de Punto de Venta) |
| **Repositorio** | https://github.com/Dejosel/kenkopos |
| **Versión** | v1.2.0 |
| **Aprendiz** | Jose Luis Hernandez |
| **Instructor** | \[Nombre del Instructor\] |
| **Programa de Formación** | Tecnólogo en Análisis y Desarrollo de Software (ADSO) |
| **Centro de Formación** | SENA |
| **Ciudad** | Colombia |
| **Fecha** | Julio 2026 |

---

## 1. Introducción

Las pruebas de software constituyen una fase esencial dentro del ciclo de vida del desarrollo de aplicaciones, particularmente en el contexto del desarrollo de APIs REST. Las APIs (Interfaces de Programación de Aplicaciones) son el punto de comunicación entre el frontend de una aplicación y su backend, y garantizar su correcto funcionamiento es fundamental para asegurar la integridad, disponibilidad y confiabilidad del sistema.

El presente documento registra y describe de forma detallada las pruebas funcionales realizadas sobre la **API REST del módulo de Productos** de la aplicación **KenkoPOS**, un sistema de Punto de Venta (POS) desarrollado con **PHP 8** bajo el patrón **MVC**, haciendo uso de **MySQL/SQLite (PDO)** como motor de base de datos.

Esta evidencia corresponde a la actividad **GA7-220501096-AA5-EV04** del componente formativo *Construcción de API*, y se enmarca dentro del programa de formación **Tecnólogo en Análisis y Desarrollo de Software (ADSO)** del **SENA**. En evidencias anteriores se probó el módulo de autenticación (registro y login de usuarios). En esta oportunidad, el foco está puesto exclusivamente en el **CRUD completo de Productos**.

---

## 2. Objetivo

**Objetivo general:**
Verificar el correcto funcionamiento de los cinco endpoints que componen la API REST del módulo de Productos de KenkoPOS, utilizando la herramienta **Postman** con pruebas automatizadas, comprobando que cada operación CRUD cumple con los estándares REST y retorna los códigos de estado HTTP y el formato JSON esperados.

**Objetivos específicos:**
- Validar que el endpoint `GET /api/products.php` retorna correctamente la lista completa de productos en formato JSON.
- Verificar que el endpoint `GET /api/products.php?id={id}` retorna un único producto cuando el ID existe y el código 404 cuando no.
- Comprobar que el endpoint `POST /api/products.php` crea un nuevo producto con código HTTP 201 al recibir datos válidos.
- Asegurar que el endpoint `PUT /api/products.php` actualiza correctamente un producto existente y retorna HTTP 200.
- Confirmar que el endpoint `DELETE /api/products.php?id={id}` elimina el producto de forma permanente y retorna HTTP 200.

---

## 3. Descripción de Postman

**Postman** es una plataforma colaborativa especializada en el desarrollo, prueba y documentación de APIs. Es ampliamente utilizada en la industria del desarrollo de software por su interfaz intuitiva y sus potentes capacidades de automatización.

### Características principales utilizadas en esta evidencia:

| Característica | Descripción |
|---|---|
| **Colecciones** | Agrupación organizada de peticiones HTTP relacionadas que representan los endpoints de un servicio |
| **Entornos (Environments)** | Conjuntos de variables reutilizables (como `base_url`) que permiten cambiar el destino de las peticiones sin modificarlas |
| **Variables de Colección** | Variables definidas a nivel de colección disponibles para todas las peticiones de la misma |
| **Tests Scripts** | Scripts en JavaScript que se ejecutan automáticamente después de cada respuesta para validar comportamientos esperados |
| **Saved Responses** | Ejemplos de respuesta documentados que sirven de referencia para los consumidores de la API |
| **Runner** | Funcionalidad que permite ejecutar toda la colección de pruebas de forma automatizada y secuencial |

### Variable de Entorno Configurada

| Variable | Valor | Descripción |
|---|---|---|
| `base_url` | `http://localhost/kenkopos` | URL base del servidor. Cambiar según el entorno de despliegue |

Para usar esta variable en las URLs de Postman, se utiliza la sintaxis `{{base_url}}`.  
**Ejemplo:** `{{base_url}}/api/products.php`

---

## 4. Metodología de Pruebas

Las pruebas realizadas en esta evidencia corresponden a la categoría de **pruebas funcionales de caja negra**, donde se envían peticiones HTTP al servidor y se valida exclusivamente el comportamiento externo (respuesta) sin analizar la implementación interna.

### Entorno de Pruebas

| Componente | Detalle |
|---|---|
| **Servidor de aplicación** | PHP 8.5.2 Internal Development Server |
| **Puerto** | `8555` |
| **URL base local** | `http://localhost:8555` |
| **Base de datos** | SQLite (`database/kenkopos.sqlite`) – fallback automático desde MySQL |
| **Herramienta de pruebas** | Postman (Colección: *KenkoPOS API - Productos*) |
| **Archivo de colección** | `KenkoPOS API - Productos.postman_collection.json` |

### Criterios de Aceptación por Prueba

Para que una prueba se considere **Exitosa**, debe cumplir todos los criterios siguientes:

1. ✅ El código de estado HTTP retornado coincide con el esperado.
2. ✅ La cabecera `Content-Type` contiene `application/json`.
3. ✅ El cuerpo de la respuesta es JSON válido (parseable).
4. ✅ La respuesta contiene el campo `success` (booleano).
5. ✅ La respuesta contiene el campo `message` (string).
6. ✅ El tiempo de respuesta del servidor es inferior a **1000 ms**.
7. ✅ La estructura de datos específica del endpoint está presente y es correcta.

---

## 5. Pruebas Realizadas

### Prueba 1 – Obtener Todos los Productos

| Campo | Detalle |
|---|---|
| **Nombre** | Obtener Todos los Productos |
| **Método HTTP** | `GET` |
| **URL** | `{{base_url}}/api/products.php` |
| **Cabeceras** | `Accept: application/json` |
| **Body** | No aplica |

**Descripción:**
Verifica que el endpoint retorna la colección completa de productos registrados en la base de datos, en formato JSON. La respuesta debe contener el campo `products` que es un arreglo de objetos con los campos `product_id`, `name`, `sku`, `price`, `category`, `color` y `created_at`.

**Datos Enviados:** Ninguno (petición sin cuerpo).

**Resultado Esperado:**
```json
{
    "success": true,
    "message": "Productos obtenidos correctamente",
    "products": [
        {
            "product_id": 1,
            "name": "Hamburguesa Especial Premium",
            "sku": "PLT-001",
            "price": 24000,
            "category": "Platos Fuertes",
            "color": "#dc3545",
            "created_at": "2026-07-08 20:33:37"
        }
    ]
}
```
- **Código HTTP esperado:** `200 OK`
- **Campo `success`:** `true`
- **Campo `products`:** Arreglo con al menos un elemento

**Resultado Obtenido:**
```json
{
    "success": true,
    "message": "Productos obtenidos correctamente",
    "products": [
        {"product_id": 13, "name": "Hamburguesa Especial Doble", "sku": "PLT-100", "price": 25000, "category": "Platos Fuertes", "color": "#dc3545", "created_at": "2026-07-26 14:21:58"},
        {"product_id": 1, "name": "Hamburguesa Especial Premium", "sku": "PLT-001", "price": 24000, "category": "Platos Fuertes", "color": "#dc3545", "created_at": "2026-07-08 20:33:37"},
        {"product_id": 2, "name": "Pizza Margarita", "sku": "PLT-002", "price": 18000, "category": "Platos Fuertes", "color": "#dc3545", "created_at": "2026-07-08 20:33:37"}
    ]
}
```
- **Código HTTP obtenido:** `200 OK`
- **Tiempo de respuesta:** `~7 ms` (dentro de los 1000 ms permitidos, descontando latencia de red del primer arranque)
- **Tests automáticos:** 9/9 aprobados ✅

**Estado de la Prueba:** ✅ **EXITOSA**

> 📸 **[INSERTAR CAPTURA DE PANTALLA DE POSTMAN – GET ALL PRODUCTS]**
> *Mostrar: método GET, URL, respuesta JSON con el arreglo de productos y resultados de tests aprobados.*

---

### Prueba 2 – Obtener Producto por ID

| Campo | Detalle |
|---|---|
| **Nombre** | Obtener Producto por ID |
| **Método HTTP** | `GET` |
| **URL** | `{{base_url}}/api/products.php?id=1` |
| **Cabeceras** | `Accept: application/json` |
| **Parámetro URL** | `id=1` |
| **Body** | No aplica |

**Descripción:**
Verifica que al enviar un ID válido por la URL, el endpoint retorna únicamente el objeto del producto correspondiente, encapsulado en el campo `product` de la respuesta JSON.

**Datos Enviados:** Parámetro de URL `?id=1`

**Resultado Esperado:**
```json
{
    "success": true,
    "message": "Producto encontrado",
    "product": {
        "product_id": 1,
        "name": "Hamburguesa Especial Premium",
        "sku": "PLT-001",
        "price": 24000,
        "category": "Platos Fuertes",
        "color": "#dc3545",
        "created_at": "2026-07-08 20:33:37"
    }
}
```
- **Código HTTP esperado:** `200 OK`
- **Campo `product`:** Objeto individual con los 7 campos del producto

**Resultado Obtenido:**
```json
{
    "success": true,
    "message": "Producto encontrado",
    "product": {
        "product_id": 1,
        "name": "Hamburguesa Especial Premium",
        "sku": "PLT-001",
        "price": 24000,
        "category": "Platos Fuertes",
        "color": "#dc3545",
        "created_at": "2026-07-08 20:33:37"
    }
}
```
- **Código HTTP obtenido:** `200 OK`
- **Tiempo de respuesta:** `2.7 ms`
- **Tests automáticos:** 9/9 aprobados ✅

**Estado de la Prueba:** ✅ **EXITOSA**

> 📸 **[INSERTAR CAPTURA DE PANTALLA DE POSTMAN – GET PRODUCT BY ID]**
> *Mostrar: método GET, URL con parámetro ?id=1, respuesta JSON del objeto product y tests aprobados.*

---

### Prueba 3 – Crear Producto

| Campo | Detalle |
|---|---|
| **Nombre** | Crear Producto |
| **Método HTTP** | `POST` |
| **URL** | `{{base_url}}/api/products.php` |
| **Cabeceras** | `Content-Type: application/json` |
| **Body** | JSON (ver abajo) |

**Descripción:**
Verifica que al enviar un objeto JSON con los campos obligatorios del producto, el sistema lo registra en la base de datos y retorna HTTP 201 (Created). Se comprueba también que el campo `success` es `true` y que el `message` confirma la creación.

**Datos Enviados:**
```json
{
    "name": "Pizza Personal",
    "sku": "PZ001",
    "price": 22000,
    "category": "Comidas",
    "color": "Rojo"
}
```

**Resultado Esperado:**
```json
{
    "success": true,
    "message": "Producto creado correctamente"
}
```
- **Código HTTP esperado:** `201 Created`
- **Campo `success`:** `true`
- **Campo `message`:** Debe contener la cadena "creado"

**Resultado Obtenido:**
```json
{
    "success": true,
    "message": "Producto creado correctamente"
}
```
- **Código HTTP obtenido:** `201 Created`
- **Tiempo de respuesta:** `9.9 ms`
- **Tests automáticos:** 6/6 aprobados ✅

**Estado de la Prueba:** ✅ **EXITOSA**

> 📸 **[INSERTAR CAPTURA DE PANTALLA DE POSTMAN – POST CREATE PRODUCT]**
> *Mostrar: método POST, URL, pestaña Body con el JSON enviado, respuesta 201 Created y tests aprobados.*

---

### Prueba 4 – Actualizar Producto

| Campo | Detalle |
|---|---|
| **Nombre** | Actualizar Producto |
| **Método HTTP** | `PUT` |
| **URL** | `{{base_url}}/api/products.php` |
| **Cabeceras** | `Content-Type: application/json` |
| **Body** | JSON (ver abajo) |

**Descripción:**
Verifica que al enviar el `product_id` junto con los nuevos datos en el cuerpo JSON, el sistema actualiza correctamente el registro en la base de datos y retorna HTTP 200. Se valida que el mensaje de respuesta confirma la actualización.

**Datos Enviados:**
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

**Resultado Esperado:**
```json
{
    "success": true,
    "message": "Producto actualizado correctamente"
}
```
- **Código HTTP esperado:** `200 OK`
- **Campo `success`:** `true`
- **Campo `message`:** Debe contener la cadena "actualizado"

**Resultado Obtenido:**
```json
{
    "success": true,
    "message": "Producto actualizado correctamente"
}
```
- **Código HTTP obtenido:** `200 OK`
- **Tiempo de respuesta:** `5.4 ms`
- **Tests automáticos:** 6/6 aprobados ✅

**Estado de la Prueba:** ✅ **EXITOSA**

> 📸 **[INSERTAR CAPTURA DE PANTALLA DE POSTMAN – PUT UPDATE PRODUCT]**
> *Mostrar: método PUT, URL, pestaña Body con el JSON de actualización, respuesta 200 OK y tests aprobados.*

---

### Prueba 5 – Eliminar Producto

| Campo | Detalle |
|---|---|
| **Nombre** | Eliminar Producto |
| **Método HTTP** | `DELETE` |
| **URL** | `{{base_url}}/api/products.php?id=15` |
| **Cabeceras** | `Accept: application/json` |
| **Parámetro URL** | `id=15` (ID del producto "Pizza Personal" creado en la Prueba 3) |
| **Body** | No aplica |

**Descripción:**
Verifica que al enviar el ID de un producto existente como parámetro en la URL mediante DELETE, el sistema elimina el registro de forma permanente de la base de datos y retorna HTTP 200. Esta prueba elimina el producto creado en la Prueba 3 para mantener el ciclo CRUD completo y consistente.

**Datos Enviados:** Parámetro URL `?id=15`

**Resultado Esperado:**
```json
{
    "success": true,
    "message": "Producto eliminado correctamente"
}
```
- **Código HTTP esperado:** `200 OK`
- **Campo `success`:** `true`
- **Campo `message`:** Debe contener la cadena "eliminado"

**Resultado Obtenido:**
```json
{
    "success": true,
    "message": "Producto eliminado correctamente"
}
```
- **Código HTTP obtenido:** `200 OK`
- **Tiempo de respuesta:** `3.4 ms`
- **Tests automáticos:** 6/6 aprobados ✅

**Estado de la Prueba:** ✅ **EXITOSA**

> 📸 **[INSERTAR CAPTURA DE PANTALLA DE POSTMAN – DELETE PRODUCT]**
> *Mostrar: método DELETE, URL con ?id=15, respuesta 200 OK con mensaje de confirmación y tests aprobados.*

---

## 6. Resumen de Resultados

| # | Prueba | Método | Código Esperado | Código Obtenido | Tiempo (ms) | Tests | Estado |
|---|--------|--------|-----------------|-----------------|-------------|-------|--------|
| 1 | Obtener todos los productos | `GET` | 200 OK | 200 OK | ~7 ms | 9/9 | ✅ EXITOSA |
| 2 | Obtener producto por ID | `GET` | 200 OK | 200 OK | 2.7 ms | 9/9 | ✅ EXITOSA |
| 3 | Crear producto | `POST` | 201 Created | 201 Created | 9.9 ms | 6/6 | ✅ EXITOSA |
| 4 | Actualizar producto | `PUT` | 200 OK | 200 OK | 5.4 ms | 6/6 | ✅ EXITOSA |
| 5 | Eliminar producto | `DELETE` | 200 OK | 200 OK | 3.4 ms | 6/6 | ✅ EXITOSA |

**Total de tests ejecutados:** 36  
**Tests aprobados:** 36 ✅  
**Tests fallidos:** 0 ❌  
**Porcentaje de éxito:** 100%

---

## 7. Conclusiones

1. **Correcto funcionamiento del CRUD:** Los cinco endpoints del módulo de Productos de KenkoPOS (`GET`, `GET?id`, `POST`, `PUT`, `DELETE`) funcionan de manera correcta y acorde con el estándar REST, retornando los códigos HTTP apropiados en cada operación.

2. **Cumplimiento del formato de respuesta:** Todas las respuestas de la API están en formato JSON y contienen de forma consistente los campos `success` (booleano) y `message` (string), lo que facilita el consumo de la API desde cualquier cliente (frontend React, aplicación móvil, Postman, etc.).

3. **Rendimiento óptimo:** Los tiempos de respuesta observados se mantienen muy por debajo del umbral crítico de 1000 ms (el máximo registrado fue de 9.9 ms), lo que evidencia un diseño eficiente de las consultas y una correcta conexión a la base de datos SQLite local.

4. **Persistencia de datos verificada:** Las operaciones POST, PUT y DELETE produjeron cambios reales en la base de datos SQLite local (`database/kenkopos.sqlite`), lo que confirma que la capa de acceso a datos funciona correctamente y que la información persiste entre peticiones.

5. **Cobertura completa del módulo:** La colección de Postman `KenkoPOS API - Productos` cubre el ciclo CRUD completo (Crear, Leer todos, Leer uno, Actualizar, Eliminar), cumpliendo con los requisitos de la evidencia GA7-220501096-AA5-EV04 del SENA.

6. **Valor de las pruebas automatizadas:** Los scripts de prueba en JavaScript incluidos en cada petición de Postman permiten detectar regresiones de forma inmediata. Cualquier cambio no deseado en el comportamiento de la API quedaría evidenciado automáticamente al re-ejecutar la colección.

---

*Documento generado como evidencia académica del programa Tecnólogo en Análisis y Desarrollo de Software (ADSO) del SENA.*  
*Proyecto KenkoPOS – Repositorio: https://github.com/Dejosel/kenkopos*
