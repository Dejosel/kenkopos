# Informe Técnico: Diseño y Desarrollo de Servicios Web (API)
## Evidencia: GA7-220501096-AA5-EV03 - Proyecto

**Proyecto:** KenkoPOS  
**Repositorio:** https://github.com/Dejosel/kenkopos  
**Versión:** v1.2.0  
**Fecha:** Julio 2026  
**Aprendiz:** Jose Luis Hernandez  
**Programa:** Tecnólogo en Análisis y Desarrollo de Software (ADSO)  
**Evidencia:** GA7-220501096-AA5-EV03 - Diseño y desarrollo de servicios web - proyecto  

---

## 1. Introducción

El presente informe detalla el **diseño y desarrollo de los servicios web (APIs)** para el proyecto **KenkoPOS**, un sistema de Punto de Venta (POS) diseñado para la administración ágil de productos, ventas y autenticación de usuarios.

Siguiendo las pautas establecidas en el componente formativo **"Construcción de API"**, esta evidencia describe detalladamente la arquitectura de software, el diseño de la base de datos relacional, la especificación completa de los endpoints RESTful implementados, y las medidas de seguridad incorporadas para proteger el intercambio de datos.

---

## 2. Arquitectura de los Servicios Web

El backend de KenkoPOS está diseñado bajo una arquitectura limpia y modular en **PHP 8 Orientado a Objetos (POO)** y base de datos relacional MySQL/SQLite a través de **PDO (PHP Data Objects)**.

### Características de la Arquitectura:
- **Patrón MVC (Modelo-Vista-Controlador):** Los controladores en `api/controllers/` manejan la lógica de negocio, los modelos en `api/models/` y `app/Models/` interactúan directamente con la base de datos, y las respuestas son devueltas en formato JSON estandarizado.
- **Inyección de Dependencias:** El controlador `AuthController` recibe por constructor la instancia del modelo `User`, promoviendo el desacoplamiento y facilitando las pruebas unitarias.
- **Adaptador de Conexión a Base de Datos (Singleton/Fallback):** La clase principal de conexión `Config\Database` implementa una estrategia de tolerancia a fallos: intenta conectarse a un servidor MySQL remoto (InfinityFree) y, si detecta una caída de red o timeout, realiza un **fallback automático** a una base de datos SQLite local (`database/kenkopos.sqlite`), auto-creando las tablas y sembrando datos iniciales en caso de ser necesario.

```
       [ Cliente / React Frontend o Postman ]
                        │
                        ▼  Peticiones HTTP (JSON / CORS)
               [ Front Controllers ]
          (api/register.php, api/login.php, api/products.php)
                        │
                        ▼  Llamados a Métodos
               [ Controllers / Models ]
           (AuthController.php, Product.php, User.php)
                        │
                        ▼  Acceso Seguro (Prepared Statements)
                [ PDO Connection ]
           (config/database.php - mysql/sqlite)
                        │
                        ▼
               [ Base de Datos ] (MySQL / SQLite)
```

---

## 3. Esquema de Base de Datos

Las tablas involucradas en los servicios web de KenkoPOS son `users` (para el módulo de autenticación) y `products` (para la gestión del catálogo).

### Tabla: `users`
Almacena la información de los usuarios autorizados para operar el sistema.
- **id** (INTEGER / INT): Clave primaria, autoincrementable.
- **name** (VARCHAR 100): Nombre completo del usuario.
- **email** (VARCHAR 100): Correo electrónico del usuario (Índice ÚNICO).
- **password** (VARCHAR 255): Hash de la contraseña cifrada mediante `PASSWORD_DEFAULT` de PHP.
- **created_at** (TIMESTAMP): Fecha y hora de registro.

### Tabla: `products`
Almacena el catálogo de productos disponibles en el POS.
- **product_id** (INTEGER / INT): Clave primaria, autoincrementable.
- **name** (VARCHAR 255): Nombre del producto.
- **sku** (VARCHAR 50): Código de inventario único (Stock Keeping Unit).
- **price** (DECIMAL 10,2): Precio unitario del producto.
- **category** (VARCHAR 100): Categoría de clasificación (ej: Platos Fuertes, Bebidas, etc.).
- **color** (VARCHAR 50): Código de color hexadecimal para la interfaz de botones rápida del POS.
- **created_at** (TIMESTAMP): Fecha y hora de inserción del producto.

---

## 4. Especificación Técnica de las APIs (Endpoints)

La API opera enteramente bajo el protocolo HTTP, utilizando JSON en el cuerpo de las peticiones (`Request Body`) y en las respuestas (`Response Body`).

### 4.1. Grupo: Autenticación de Usuarios

#### A. Registro de Usuario
- **Ruta:** `/api/register.php`
- **Método HTTP:** `POST`
- **Cabeceras obligatorias:**
  - `Content-Type: application/json`
- **Cuerpo de la Petición (JSON):**
```json
{
    "name": "Jose Hernandez",
    "email": "jose@kenkopos.com",
    "password": "miPasswordSeguro123"
}
```
- **Respuesta Exitosa (HTTP 201 Created):**
```json
{
    "success": true,
    "message": "Usuario registrado correctamente"
}
```
- **Respuestas de Error:**
  - **HTTP 400 Bad Request (Faltan datos):** `{"success":false,"message":"Faltan campos obligatorios"}`
  - **HTTP 400 Bad Request (Email Inválido):** `{"success":false,"message":"Formato de correo electrónico inválido"}`
  - **HTTP 400 Bad Request (Contraseña corta):** `{"success":false,"message":"La contraseña debe tener mínimo 6 caracteres"}`
  - **HTTP 409 Conflict (Email duplicado):** `{"success":false,"message":"El usuario ya se encuentra registrado con este correo"}`

#### B. Inicio de Sesión (Login)
- **Ruta:** `/api/login.php`
- **Método HTTP:** `POST`
- **Cabeceras obligatorias:**
  - `Content-Type: application/json`
- **Cuerpo de la Petición (JSON):**
```json
{
    "email": "jose@kenkopos.com",
    "password": "miPasswordSeguro123"
}
```
- **Respuesta Exitosa (HTTP 200 OK):**
```json
{
    "success": true,
    "message": "Autenticación satisfactoria",
    "user": {
        "id": 1,
        "name": "Jose Hernandez"
    }
}
```
- **Respuestas de Error:**
  - **HTTP 401 Unauthorized (Credenciales Incorrectas):** `{"success":false,"message":"Error en la autenticación"}`
  - **HTTP 400 Bad Request (Faltan campos):** `{"success":false,"message":"Faltan campos obligatorios"}`

---

### 4.2. Grupo: CRUD de Catálogo de Productos

#### A. Listar Todos los Productos
- **Ruta:** `/api/products.php`
- **Método HTTP:** `GET`
- **Respuesta Exitosa (HTTP 200 OK):**
```json
{
    "success": true,
    "message": "Productos obtenidos correctamente",
    "products": [
        {
            "product_id": 1,
            "name": "Hamburguesa Especial",
            "sku": "PLT-001",
            "price": 22000.00,
            "category": "Platos Fuertes",
            "color": "#dc3545",
            "created_at": "2026-07-26 09:00:00"
        }
    ]
}
```

#### B. Obtener Producto Específico por ID
- **Ruta:** `/api/products.php?id={product_id}` (ej. `/api/products.php?id=1`)
- **Método HTTP:** `GET`
- **Respuesta Exitosa (HTTP 200 OK):**
```json
{
    "success": true,
    "message": "Producto encontrado",
    "product": {
        "product_id": 1,
        "name": "Hamburguesa Especial",
        "sku": "PLT-001",
        "price": 22000.00,
        "category": "Platos Fuertes",
        "color": "#dc3545",
        "created_at": "2026-07-26 09:00:00"
    }
}
```
- **Respuesta de Error (HTTP 404 Not Found):**
```json
{
    "success": false,
    "message": "Producto no encontrado"
}
```

#### C. Crear un Nuevo Producto
- **Ruta:** `/api/products.php`
- **Método HTTP:** `POST`
- **Cabeceras obligatorias:**
  - `Content-Type: application/json`
- **Cuerpo de la Petición (JSON):**
```json
{
    "name": "Tacos de Carne (3 und)",
    "sku": "PLT-005",
    "price": 17500.00,
    "category": "Platos Fuertes",
    "color": "#dc3545"
}
```
- **Respuesta Exitosa (HTTP 201 Created):**
```json
{
    "success": true,
    "message": "Producto creado correctamente"
}
```
- **Respuesta de Error (HTTP 400 Bad Request - Datos incompletos):**
```json
{
    "success": false,
    "message": "Datos incompletos. Nombre, SKU, precio y categoría son obligatorios."
}
```

#### D. Actualizar Producto Existente
- **Ruta:** `/api/products.php`
- **Método HTTP:** `PUT`
- **Cabeceras obligatorias:**
  - `Content-Type: application/json`
- **Cuerpo de la Petición (JSON):**
```json
{
    "product_id": 1,
    "name": "Hamburguesa Doble Queso",
    "sku": "PLT-001",
    "price": 24000.00,
    "category": "Platos Fuertes",
    "color": "#dc3545"
}
```
- **Respuesta Exitosa (HTTP 200 OK):**
```json
{
    "success": true,
    "message": "Producto actualizado correctamente"
}
```
- **Respuesta de Error (HTTP 404 Not Found - ID inexistente):**
```json
{
    "success": false,
    "message": "Producto no encontrado para actualizar."
}
```

#### E. Eliminar un Producto
- **Ruta:** `/api/products.php?id={product_id}`
- **Método HTTP:** `DELETE`
- **Respuesta Exitosa (HTTP 200 OK):**
```json
{
    "success": true,
    "message": "Producto eliminado correctamente"
}
```
- **Respuesta de Error (HTTP 404 Not Found):**
```json
{
    "success": false,
    "message": "Producto no encontrado para eliminar."
}
```

---

## 5. Medidas de Seguridad y Estándares Implementados

Con el fin de asegurar el cumplimiento de las mejores prácticas descritas en el componente formativo de Construcción de APIs, se programaron las siguientes directivas de seguridad en el código del servidor:

1. **Prevención de Inyección SQL (Prepared Statements):** La API utiliza PDO y sentencias preparadas mediante `prepare()` y `bindParam()`. Esto evita que parámetros maliciosos alteren las consultas SQL.
2. **Cifrado Seguro de Contraseñas (Hashing):** En el registro, la contraseña es hasheada usando `password_hash($password, PASSWORD_DEFAULT)` antes de su almacenamiento. En el inicio de sesión, se verifica mediante la función nativa segura `password_verify()`.
3. **Control de Acceso de Origen Cruzado (CORS):** El script configure headers HTTP específicos para permitir peticiones AJAX provenientes de cualquier dominio (`Access-Control-Allow-Origin: *`) y admite los métodos RESTful (`GET, POST, PUT, DELETE, OPTIONS`).
4. **Saneamiento e Higiene de Datos:** Se emplean las funciones `htmlspecialchars()` y `strip_tags()` en los modelos de datos (`User.php` y `Product.php`) para mitigar ataques XSS (Cross-Site Scripting) limpiando el código HTML insertado en inputs de texto.
5. **Respuestas de Error Seguras:** La API no revela detalles de la infraestructura interna de bases de datos. Si una consulta falla, devuelve un código HTTP apropiado (ej: 500) y un mensaje genérico.

---

## 6. Control de Versiones y Repositorio Git

El proyecto de servicios web se versiona de manera rigurosa utilizando la herramienta **Git**:
- **Repositorio Remoto:** Alojado en GitHub bajo la dirección: `https://github.com/Dejosel/kenkopos`.
- **Estructura de Ramas:** Se trabaja en la rama `main` de producción.
- **Tags de Entrega:** Cada evidencia clave se etiqueta formalmente en Git. Para esta entrega de servicios web de la EV03, se crea el tag de versión **`v1.2.0`**.

---

## 7. Conclusiones

- El diseño de los servicios web (APIs) de **KenkoPOS** se adhiere plenamente a los principios de la arquitectura RESTful, utilizando de manera coherente los métodos HTTP (GET, POST, PUT, DELETE) y el formato estándar de comunicación JSON.
- La separación de responsabilidades a través de controladores y modelos en PHP Orientado a Objetos permite un código limpio, mantenible y robusto.
- Las medidas implementadas en el backend (PDO prepared statements, hashing de contraseñas, CORS headers) garantizan la protección básica de los recursos y la comunicación segura con el cliente React frontend.
- Se cuenta con una colección de Postman completa y estructurada en carpetas que permite validar la correcta funcionalidad de la API de manera automatizada y repetible.
