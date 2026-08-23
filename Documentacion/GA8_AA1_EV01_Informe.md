# GA8-220501096-AA1-EV01
# Desarrollar Software a partir de la Integración de sus Módulos Componentes

---

## PORTADA

|  |  |
|---|---|
| **Título** | Desarrollar Software a partir de la Integración de sus Módulos Componentes |
| **Evidencia** | GA8-220501096-AA1-EV01 |
| **Proyecto** | KenkoPOS – Sistema de Punto de Venta Web |
| **Repositorio** | https://github.com/Dejosel/kenkopos |
| **Versión** | v1.3.0 |
| **Aprendiz** | Jose Luis Hernandez |
| **Instructor** | [Nombre del Instructor] |
| **Programa de Formación** | Tecnólogo en Análisis y Desarrollo de Software (ADSO) |
| **Centro de Formación** | SENA |
| **Ficha** | [Número de Ficha] |
| **Ciudad** | Colombia |
| **Fecha** | Agosto 2026 |

---

## 1. Introducción

El presente documento corresponde a la evidencia **GA8-220501096-AA1-EV01** del componente formativo *Desarrollar Software a partir de la Integración de sus Módulos Componentes*, perteneciente al programa **Tecnólogo en Análisis y Desarrollo de Software (ADSO)** del **SENA**.

**KenkoPOS** es un sistema de punto de venta (POS) orientado a la web, diseñado para pequeños y medianos negocios del sector gastronómico. La aplicación permite gestionar el inventario de productos, registrar comandas/ventas, visualizar reportes de transacciones y administrar usuarios, todo desde una interfaz web moderna y responsiva.

Esta evidencia documenta el proceso de **codificación e integración** de los módulos que componen el sistema, describiendo la arquitectura, las capas de la aplicación, los componentes desarrollados, las tecnologías y frameworks utilizados, los mecanismos de seguridad implementados, y las pruebas unitarias realizadas.

---

## 2. Objetivos

### Objetivo General
Integrar los módulos del sistema KenkoPOS mediante la codificación de sus componentes en una arquitectura web orientada a capas (MVC + API REST + Frontend SPA), cumpliendo los requerimientos funcionales y no funcionales definidos en el proyecto.

### Objetivos Específicos
- Codificar el módulo de **Autenticación** (registro e inicio de sesión de usuarios) con manejo seguro de contraseñas.
- Codificar el módulo de **Gestión de Productos** (CRUD completo) accesible desde el POS y el panel administrativo.
- Codificar el módulo de **Punto de Venta (POS)** con gestión de comandas, descuentos e impuestos.
- Codificar el módulo de **Reportes** para el historial de ventas.
- Integrar el **frontend React** con el **backend PHP** mediante una API REST JSON.
- Aplicar buenas prácticas de codificación, seguridad y control de versiones.

---

## 3. Requerimientos del Sistema

### 3.1 Requerimientos Funcionales

| ID | Módulo | Descripción |
|---|---|---|
| RF-01 | Autenticación | El sistema debe permitir el registro de nuevos usuarios con nombre, correo y contraseña |
| RF-02 | Autenticación | El sistema debe autenticar usuarios validando correo y contraseña con hash bcrypt |
| RF-03 | Productos | El sistema debe permitir crear, leer, actualizar y eliminar productos (CRUD) |
| RF-04 | Productos | Cada producto debe tener: nombre, SKU, precio, categoría y color de etiqueta |
| RF-05 | POS | El sistema debe mostrar el catálogo de productos filtrable por categoría |
| RF-06 | POS | El sistema debe permitir agregar productos al carrito con cantidad y subtotal |
| RF-07 | POS | El sistema debe calcular descuentos (%), impuestos (IVA) y total de la venta |
| RF-08 | POS | El sistema debe registrar las comandas/ventas en la base de datos |
| RF-09 | POS | El sistema debe calcular el cambio al recibir efectivo |
| RF-10 | Reportes | El sistema debe mostrar el historial completo de ventas con sus ítems |

### 3.2 Requerimientos No Funcionales

| ID | Categoría | Descripción |
|---|---|---|
| RNF-01 | Seguridad | Las contraseñas deben almacenarse con `password_hash()` (bcrypt, costo 10) |
| RNF-02 | Seguridad | Todas las consultas SQL deben usar PDO con sentencias preparadas (prevención de inyección SQL) |
| RNF-03 | Seguridad | Los datos de entrada deben sanitizarse con `htmlspecialchars()` y `strip_tags()` |
| RNF-04 | Seguridad | Las cabeceras CORS deben configurarse correctamente en cada endpoint |
| RNF-05 | Disponibilidad | El sistema debe funcionar con MySQL (producción) y SQLite (fallback local) |
| RNF-06 | Rendimiento | El tiempo de respuesta de la API debe ser inferior a 1000 ms |
| RNF-07 | Usabilidad | La interfaz debe ser responsiva y compatible con dispositivos de escritorio y tablet |
| RNF-08 | Mantenibilidad | El código debe estar organizado bajo el patrón MVC y con documentación PHPDoc/JSDoc |

---

## 4. Tecnologías y Frameworks

### 4.1 Tecnologías por Capa

```
CAPA DE PRESENTACIÓN
  React 18 + Vite + Bootstrap 5 + Bootstrap Icons
  Axios (HTTP) · React Router DOM · JSX

CAPA DE NEGOCIO / API
  PHP 8+ · Patrón MVC · API REST · JSON
  Namespaces · PDO · Bcrypt (password_hash/verify)

CAPA DE DATOS
  MySQL 8 (producción) · SQLite 3 (desarrollo/fallback)
  PDO (abstracción) · Sentencias Preparadas
```

### 4.2 Tabla de Tecnologías

| Capa | Tecnología | Versión | Propósito |
|---|---|---|---|
| Frontend | React | 18+ | Librería UI basada en componentes |
| Frontend | Vite | 5+ | Bundler y servidor de desarrollo |
| Frontend | Bootstrap | 5.3 | Framework CSS responsivo |
| Frontend | Axios | 1.x | Cliente HTTP para consumo de API |
| Frontend | React Router DOM | 6.x | Enrutamiento de página única (SPA) |
| Backend | PHP | 8.0+ | Lenguaje del servidor |
| Backend | PDO | Nativo | Abstracción de base de datos |
| Backend | PHP password_hash | Nativo | Hash bcrypt de contraseñas |
| Base de Datos | MySQL | 8.x | Motor relacional (producción) |
| Base de Datos | SQLite | 3.x | Motor embebido (desarrollo/fallback) |
| Control de Versiones | Git / GitHub | - | Repositorio y control de cambios |
| Pruebas | Postman | 11.x | Pruebas y documentación de API |

---

## 5. Arquitectura de la Aplicación

### 5.1 Patrón Arquitectural: MVC + API REST + SPA

KenkoPOS implementa una arquitectura **híbrida por capas**:

1. **Capa de Presentación (Frontend SPA):** Aplicación React que consume la API REST mediante Axios. Implementa componentes reutilizables para cada vista.

2. **Capa de Negocio (Backend MVC):** PHP 8 organizado bajo el patrón MVC. Los **Controladores** manejan la lógica de negocio, los **Modelos** interactúan con la base de datos y los **Endpoints** de la API actúan como puntos de entrada REST.

3. **Capa de Datos:** MySQL/SQLite con acceso a través de PDO. El sistema detecta automáticamente qué motor está disponible y hace el fallback correspondiente.

### 5.2 Diagrama de Arquitectura

```
Browser / Cliente
       |
       v
React Frontend  <--HTTP/JSON-->  Apache / PHP Server
(Vite SPA)                      |
/frontend/src/                  |-- Endpoints /api/*.php
                                |-- Views MVC /public/
                                |-- Controllers /app/Controllers/
                                |-- Models (PDO) /app/Models/
                                          |
                                    Base de Datos
                                MySQL (prod) / SQLite (dev)
```

---

## 6. Diagrama de Paquetes

```
kenkopos/
+-- api/                        <- Paquete API REST (capa de servicio)
|   +-- config/                 <- Configuración DB para API
|   +-- controllers/            <- Namespace Api\Controllers
|   |   +-- AuthController.php
|   +-- models/                 <- Namespace Api\Models
|   |   +-- User.php
|   |   +-- Order.php
|   +-- helpers/                <- Utilidades compartidas
|   |   +-- Response.php
|   +-- login.php               <- Endpoint POST /api/login.php
|   +-- register.php            <- Endpoint POST /api/register.php
|   +-- products.php            <- Endpoint CRUD /api/products.php
|   +-- orders.php              <- Endpoint /api/orders.php
|
+-- app/                        <- Paquete MVC (lógica de negocio)
|   +-- Controllers/            <- Namespace App\Controllers
|   |   +-- ProductController.php
|   +-- Models/                 <- Namespace App\Models
|   |   +-- Product.php
|   +-- Helpers/
|       +-- Response.php
|
+-- config/                     <- Configuración global
|   +-- database.php            <- Conexión MySQL/SQLite principal
|   +-- .htaccess
|
+-- database/                   <- Scripts y archivos de base de datos
|   +-- kenkopos.sql
|   +-- kenkopos.sqlite
|
+-- public/                     <- Vistas MVC del sistema POS
|   +-- pos.php                 <- Módulo POS (punto de venta)
|   +-- reports.php             <- Módulo de reportes
|   +-- products/               <- CRUD de productos (vistas PHP)
|   +-- assets/                 <- CSS, JS, imágenes
|
+-- frontend/                   <- Paquete Frontend SPA (React + Vite)
    +-- src/
        +-- components/         <- Componentes reutilizables
        |   +-- layouts/        <- Navbar, MainLayout
        |   +-- products/       <- ProductList, ProductForm, etc.
        |   +-- ui/             <- AlertMessage, Loading, etc.
        +-- pages/              <- Páginas/vistas
        +-- services/           <- Capa de servicio HTTP (Axios)
        +-- hooks/              <- Custom hooks de React
        +-- utils/              <- Funciones utilitarias
```

---

## 7. Diagrama de Componentes

```
FRONTEND (React SPA)
  App.jsx (Router)
    --> MainLayout (Navbar + Outlet)
          --> ProductsPage
                --> ProductList (contenedor con estado)
                      --> ProductTable --> ProductRow
                      --> ProductModal --> ProductForm
                      --> DeleteModal
                      --> SearchBar / Pagination / Loading / AlertMessage
  services/api.js --> Axios (HTTP Client)
          |
          | HTTP/JSON
          v
BACKEND PHP (MVC + API REST)
  login.php / register.php --> AuthController --> User (Model/PDO)
  products.php (CRUD REST) --> ProductController --> Product (Model/PDO)
  orders.php (POS Ventas)  --> Order (Model/PDO con transacciones)
          |
          v
  PDO (Data Access Layer)
          |
          v
  MySQL / SQLite Database
  (users, products, orders, order_items)
```

---

## 8. Módulos Codificados

### 8.1 Módulo de Autenticación

**Archivos involucrados:**

| Archivo | Capa | Descripción |
|---|---|---|
| `api/register.php` | Endpoint | Recibe POST con datos de registro |
| `api/login.php` | Endpoint | Recibe POST con credenciales |
| `api/controllers/AuthController.php` | Controlador | Lógica de negocio de autenticación |
| `api/models/User.php` | Modelo | Acceso a tabla `users` en BD |

**Funcionalidades:**
- Registro de usuario con validación de email (`filter_var`), longitud de contraseña y unicidad.
- Almacenamiento de contraseña con **`password_hash($pass, PASSWORD_DEFAULT)`** (bcrypt, costo 10).
- Login con verificación mediante **`password_verify()`**.
- Soporte de roles: `admin`, `mesero`.

**Flujo del Registro:**
```
POST /api/register.php
   |
   v
AuthController::register()
   +-- Validar campos obligatorios (name, email, password)
   +-- Validar formato de email
   +-- Validar longitud de contraseña (>=6 chars)
   +-- Verificar si email ya existe (User::findByEmail)
   +-- User::register() -> INSERT INTO users (hash bcrypt)
          +-- Response::success(201) o Response::error(4xx/5xx)
```

---

### 8.2 Módulo de Gestión de Productos

**Archivos involucrados:**

| Archivo | Capa | Descripción |
|---|---|---|
| `api/products.php` | Endpoint REST | CRUD completo vía HTTP methods |
| `app/Models/Product.php` | Modelo | ORM manual para tabla `products` |
| `app/Controllers/ProductController.php` | Controlador | Lógica para vistas MVC y API |
| `frontend/src/components/products/ProductList.jsx` | Vista React | Listado y gestión interactiva |
| `frontend/src/components/products/ProductForm.jsx` | Vista React | Formulario crear/editar |
| `frontend/src/components/products/ProductTable.jsx` | Vista React | Tabla responsiva de productos |
| `frontend/src/components/products/ProductRow.jsx` | Vista React | Fila individual con acciones |
| `frontend/src/components/products/ProductModal.jsx` | Vista React | Modal que envuelve el formulario |
| `frontend/src/components/products/DeleteModal.jsx` | Vista React | Modal de confirmación de borrado |

**Endpoints REST:**

| Método | URL | Acción | Código HTTP |
|---|---|---|---|
| `GET` | `/api/products.php` | Listar todos los productos | 200 OK |
| `GET` | `/api/products.php?id={n}` | Obtener producto por ID | 200 OK / 404 |
| `POST` | `/api/products.php` | Crear nuevo producto | 201 Created |
| `PUT` | `/api/products.php` | Actualizar producto | 200 OK |
| `DELETE` | `/api/products.php?id={n}` | Eliminar producto | 200 OK |

**Campos del Producto:**

```php
class Product {
    public ?int    $product_id  // PK autoincremental
    public string  $name        // Nombre del producto (obligatorio)
    public string  $sku         // Código único (obligatorio)
    public float   $price       // Precio de venta (obligatorio, >0)
    public string  $category    // Categoría (default: 'General')
    public ?string $color       // Color de etiqueta HEX (opcional)
    public ?string $created_at  // Timestamp de creación
}
```

---

### 8.3 Módulo Punto de Venta (POS)

**Archivos involucrados:**

| Archivo | Capa | Descripción |
|---|---|---|
| `public/pos.php` | Vista PHP | Interfaz completa del POS |
| `public/assets/js/pos.js` | Frontend JS | Lógica del carrito y pagos |
| `public/assets/css/pos.css` | Estilos | Estilos del POS |
| `api/orders.php` | Endpoint REST | Registra comandas via POST |
| `api/models/Order.php` | Modelo | ORM para `orders` + `order_items` |

**Funcionalidades del POS:**
- Catálogo de productos organizado por categorías con botones de filtro.
- Carrito de compras con ajuste de cantidades y eliminación de ítems.
- Aplicación de descuentos en porcentaje sobre el subtotal.
- Cálculo de IVA/impuesto configurable (%).
- Selección de método de pago: Efectivo, Tarjeta, Transferencia.
- Cálculo automático de cambio al recibir efectivo mayor al total.
- Guardado de comanda en BD mediante **transacción PDO** (atómica): `orders` + `order_items`.
- Generación de ticket/comprobante visual post-venta.
- Indicador en tiempo real del motor de BD activo (MySQL / SQLite).

**Lógica de Cálculo (pos.js):**
```javascript
subtotal     = Suma(producto.precio x cantidad)
descuento    = subtotal x (descuento_% / 100)
baseGravable = subtotal - descuento
impuesto     = baseGravable x (iva_% / 100)
total        = baseGravable + impuesto
cambio       = efectivoRecibido - total
```

**Transacción en Base de Datos (Order::save):**
```php
$conn->beginTransaction();
  INSERT INTO orders (...campos de la venta...)
  foreach($items) {
      INSERT INTO order_items (order_id, product_id, name, sku, price, qty)
  }
$conn->commit();
// En caso de error: $conn->rollBack();
```

---

### 8.4 Módulo de Reportes

**Archivos involucrados:**

| Archivo | Capa | Descripción |
|---|---|---|
| `public/reports.php` | Vista PHP | Dashboard de historial de ventas |
| `api/orders.php` (GET) | Endpoint | Retorna todas las órdenes con ítems |

**Funcionalidades:**
- Listado de todas las ventas ordenadas por fecha descendente.
- Vista detallada de cada comanda con sus productos, cantidades y precios.
- Información del operador, mesa, método de pago, descuento, impuesto y total.
- Totales acumulados por período.

---

## 9. Mecanismos de Seguridad

### 9.1 Prevención de Inyección SQL – Sentencias Preparadas PDO

```php
// CORRECTO – Sentencia preparada (segura)
$query = "SELECT * FROM products WHERE product_id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
```

### 9.2 Contraseñas – Bcrypt

```php
// Al registrar (User::register)
$hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
// PASSWORD_DEFAULT = bcrypt con costo 10

// Al iniciar sesión (User::login)
if (password_verify($passwordPlano, $hashGuardado)) {
    // Autenticación exitosa
}
```

### 9.3 Sanitización de Entradas

```php
// Aplicado en todos los modelos antes de insertar en BD
$this->name  = htmlspecialchars(strip_tags($this->name));
$this->email = htmlspecialchars(strip_tags($this->email));
$this->role  = htmlspecialchars(strip_tags($this->role));
```

### 9.4 Cabeceras CORS

```php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
```

### 9.5 Protección de Directorios

```apache
# /app/.htaccess – Bloquea acceso directo a archivos PHP de la capa de negocio
Require all denied
```

### 9.6 Validación en Controladores

```php
// AuthController.php
if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    Response::error(400, "Formato de correo electrónico inválido");
    return;
}
if (strlen(trim($data->password)) < 6) {
    Response::error(400, "La contraseña debe tener mínimo 6 caracteres");
    return;
}
```

---

## 10. Esquema de Base de Datos

### 10.1 Tablas del Sistema

**Tabla `users`:**
```sql
CREATE TABLE IF NOT EXISTS users (
  id         INT(11)      NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(100) NOT NULL,
  password   VARCHAR(255) NOT NULL,  -- Hash bcrypt
  role       VARCHAR(20)  NOT NULL DEFAULT 'mesero',
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Tabla `products`:**
```sql
CREATE TABLE IF NOT EXISTS products (
  product_id INT(11)       NOT NULL AUTO_INCREMENT,
  name       VARCHAR(150)  NOT NULL,
  sku        VARCHAR(50)   NOT NULL,
  price      DECIMAL(10,2) NOT NULL,
  category   VARCHAR(100)  NOT NULL DEFAULT 'General',
  color      VARCHAR(30)   DEFAULT NULL,
  created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Tabla `orders` (Comandas):**
```sql
CREATE TABLE IF NOT EXISTS orders (
  order_id         INT(11)       NOT NULL AUTO_INCREMENT,
  table_name       VARCHAR(100)  NOT NULL,
  subtotal         DECIMAL(10,2) NOT NULL,
  discount_percent INT(3)        NOT NULL DEFAULT 0,
  discount_amount  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  tax_percent      INT(3)        NOT NULL DEFAULT 0,
  tax_amount       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total            DECIMAL(10,2) NOT NULL,
  payment_method   VARCHAR(50)   NOT NULL,
  cash_received    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  change_amount    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  operator_name    VARCHAR(100)  NOT NULL DEFAULT 'Admin',
  operator_role    VARCHAR(50)   NOT NULL DEFAULT 'admin',
  created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Tabla `order_items` (Ítems de la Comanda):**
```sql
CREATE TABLE IF NOT EXISTS order_items (
  item_id    INT(11)       NOT NULL AUTO_INCREMENT,
  order_id   INT(11)       NOT NULL,
  product_id INT(11)       NOT NULL,
  name       VARCHAR(150)  NOT NULL,
  sku        VARCHAR(50)   NOT NULL DEFAULT '',
  price      DECIMAL(10,2) NOT NULL,
  qty        INT(11)       NOT NULL,
  PRIMARY KEY (item_id),
  FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 10.2 Diagrama Entidad-Relación (DER)

```
users                  products
-----                  --------
id (PK)                product_id (PK)
name                   name
email (UNIQUE)         sku
password               price
role                   category
created_at             color
                       created_at

orders ----1:N---- order_items
------             -----------
order_id (PK)      item_id (PK)
table_name         order_id (FK -> orders)
subtotal           product_id
discount_percent   name
discount_amount    sku
tax_percent        price
tax_amount         qty
total
payment_method
cash_received
change_amount
operator_name
operator_role
created_at
```

---

## 11. Formato de Respuesta de la API

Todas las respuestas siguen el mismo formato JSON estandarizado:

```php
// Helper Response.php
class Response {
    public static function success(int $code, string $message, array $data = []): void {
        http_response_code($code);
        $response = ['success' => true, 'message' => $message];
        if (!empty($data)) $response = array_merge($response, $data);
        echo json_encode($response);
        exit;
    }

    public static function error(int $code, string $message): void {
        http_response_code($code);
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}
```

**Respuesta exitosa:**
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

**Respuesta de error:**
```json
{
  "success": false,
  "message": "Datos incompletos. Nombre, SKU, precio y categoría son obligatorios."
}
```

---

## 12. Capa de Servicio Frontend (api.js)

```javascript
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost/kenkopos';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Interceptor global de errores
api.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error("API Error:", error.response?.data?.message || error.message);
    return Promise.reject(error);
  }
);

export default api;
```

**Uso en componentes React:**
```javascript
// Listar
const response = await api.get('/api/products.php');
// Crear
const res = await api.post('/api/products.php', { name, sku, price, category, color });
// Actualizar
const res = await api.put('/api/products.php', { product_id, ...formData });
// Eliminar
const res = await api.delete(`/api/products.php?id=${id}`);
```

---

## 13. Componentes React Reutilizables

### 13.1 Componentes de Productos

| Componente | Responsabilidad |
|---|---|
| `ProductList.jsx` | Contenedor principal. Maneja estado, peticiones HTTP, paginación y modales |
| `ProductTable.jsx` | Tabla responsiva que recibe props y delega eventos al contenedor |
| `ProductRow.jsx` | Fila individual con botones Editar / Eliminar |
| `ProductForm.jsx` | Formulario controlado para crear y editar productos |
| `ProductModal.jsx` | Modal Bootstrap que envuelve el `ProductForm` |
| `DeleteModal.jsx` | Modal de confirmación antes de eliminar |

### 13.2 Componentes UI Reutilizables

| Componente | Responsabilidad |
|---|---|
| `SearchBar.jsx` | Input de búsqueda con icono, filtra en tiempo real |
| `Pagination.jsx` | Paginación numérica con botones anterior/siguiente |
| `Loading.jsx` | Spinner de carga mientras se espera la API |
| `AlertMessage.jsx` | Alerta de éxito/error con cierre manual |
| `EmptyState.jsx` | Estado vacío cuando no hay datos (con acción opcional) |

### 13.3 Layouts

| Componente | Responsabilidad |
|---|---|
| `MainLayout.jsx` | Layout principal: Navbar + Outlet (React Router) |
| `Navbar.jsx` | Barra de navegación con logo y usuario actual |

---

## 14. Mapa de Navegación

```
Módulo Frontend React (SPA)
/                        -> ProductsPage (Gestión de Productos)
/* (otras rutas)         -> 404 Not Found

Módulo PHP (acceso directo)
/kenkopos/public/
  index.php              -> Redirección principal
  pos.php                -> Módulo POS (Punto de Venta)
  reports.php            -> Módulo de Reportes
  products/list.php      -> Listado de productos
  products/create.php    -> Formulario crear producto
  products/edit.php?id   -> Formulario editar producto
  products/delete.php    -> Acción eliminar (POST)

API REST
/kenkopos/api/
  register.php           -> POST (registro de usuario)
  login.php              -> POST (autenticación)
  products.php           -> GET / POST / PUT / DELETE
  orders.php             -> GET / POST
```

---

## 15. Control de Versiones – Git

**Repositorio:** https://github.com/Dejosel/kenkopos

### Historial de Commits

| Commit | Descripción |
|---|---|
| `00bac33` | Initial commit: KenkoPOS project |
| `95bc9e1` | docs(ev02): agrega documentación para evidencia GA7-220501096-AA2-EV02 |
| `59265c0` | feat(ev01): migración de KenkoPOS a Laravel (v2) |
| `529a67d` | feat: Implementación de pantalla POS, controladores y bases de datos |
| `5e4422e` | feat(ev05): implementación del servicio web para autenticación GA7-220501096-AA5-EV01 |
| `cc193c7` | feat(ev03): frontend react para el módulo de productos GA7-220501096-AA4-EV03 |
| `6832e47` | fix: reutilizar conexión principal (InfinityFree+SQLite) en módulos API |
| `82fddac` | fix: compatibilidad SQLite en User model |
| `0faea92` | docs: implement design, development and tests for web services evidence GA7-220501096-AA5-EV03 |
| `4c409aa` | test(ev04): api testing, postman collection, endpoints documentation and video script GA7-220501096-AA5-EV04 |

**Estrategia de Ramas:**
- **`main`**: Rama principal con código estable.
- Commits con convención **Conventional Commits**: `feat:`, `fix:`, `docs:`, `test:`.

---

## 16. Ambiente de Desarrollo

### 16.1 Configuración Local

| Componente | Herramienta | Versión |
|---|---|---|
| Servidor PHP | PHP Built-in Server | 8.5.2 |
| Base de datos local | SQLite | 3.x (`database/kenkopos.sqlite`) |
| Frontend dev server | Vite | 5.x |
| IDE | VS Code / Antigravity IDE | - |
| Control de versiones | Git | 2.x |
| Pruebas API | Postman | 11.x |
| Gestor de paquetes | npm | 10.x |

### 16.2 Variables de Entorno

**Backend (`config/database.php`):**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kenkopos');
// Fallback automático a SQLite si MySQL no está disponible
```

**Frontend (`frontend/.env`):**
```
VITE_API_URL=http://localhost/kenkopos
```

### 16.3 Comandos de Ejecución

```bash
# Backend (PHP built-in server)
cd kenkopos/
php -S localhost:8555

# Frontend (Vite dev server)
cd kenkopos/frontend/
npm install
npm run dev
# Disponible en: http://localhost:5173
```

---

## 17. Pruebas Unitarias de los Módulos

### 17.1 Resumen de Pruebas de la API REST (Postman)

| # | Módulo | Prueba | Método | Código Esperado | Código Obtenido | Tests | Estado |
|---|---|---|---|---|---|---|---|
| 1 | Productos | Obtener todos | `GET` | 200 OK | 200 OK | 9/9 | OK |
| 2 | Productos | Obtener por ID | `GET` | 200 OK | 200 OK | 9/9 | OK |
| 3 | Productos | Crear producto | `POST` | 201 Created | 201 Created | 6/6 | OK |
| 4 | Productos | Actualizar producto | `PUT` | 200 OK | 200 OK | 6/6 | OK |
| 5 | Productos | Eliminar producto | `DELETE` | 200 OK | 200 OK | 6/6 | OK |

**Total: 36 pruebas automáticas ejecutadas – 36 aprobadas (100%)**

### 17.2 Criterios de Aceptación

Para que una prueba se considere **exitosa** debe cumplir:

1. El código de estado HTTP retornado coincide con el esperado.
2. La cabecera `Content-Type` contiene `application/json`.
3. El cuerpo de la respuesta es JSON válido.
4. La respuesta contiene el campo `success` (booleano).
5. La respuesta contiene el campo `message` (string).
6. El tiempo de respuesta es inferior a **1000 ms**.
7. La estructura de datos específica del endpoint está presente y es correcta.

### 17.3 Entorno de Pruebas

| Componente | Detalle |
|---|---|
| Servidor de aplicación | PHP 8.5.2 Internal Development Server |
| Puerto | `8555` |
| URL base local | `http://localhost:8555` |
| Base de datos | SQLite (`database/kenkopos.sqlite`) |
| Herramienta | Postman |
| Archivo de colección | `KenkoPOS API - Productos.postman_collection.json` |

---

## 18. Buenas Prácticas de Codificación Aplicadas

### 18.1 Backend PHP

- **Namespaces** para evitar colisión: `App\Controllers`, `Api\Models`, etc.
- **Typed Properties** de PHP 8: `private PDO $conn;`, `public ?int $id = null;`
- **PHPDoc** en todos los métodos públicos con `@param` y `@return`.
- **Principio de Responsabilidad Única (SRP):** cada clase tiene una sola responsabilidad.
- **Inyección de dependencias:** modelos reciben `PDO` por constructor (no lo instancian).
- **Respuestas HTTP semánticamente correctas:** 200, 201, 400, 401, 404, 405, 409, 500.
- **Manejo de excepciones** con `try/catch (PDOException)` en todas las operaciones de BD.
- **Transacciones atómicas** en `Order::save()` con `beginTransaction/commit/rollBack`.

### 18.2 Frontend React

- **Separación de responsabilidades**: componentes contenedores vs presentacionales.
- **Gestión de estado con `useState` y `useEffect`** sin librerías innecesarias.
- **Manejo de errores** con `try/catch` y feedback visual al usuario.
- **JSDoc** en los componentes principales.
- **Código sin warnings de ESLint** (configuración con oxlint).

---

## 19. Patrones de Diseño Aplicados

| Patrón | Dónde se Aplica | Descripción |
|---|---|---|
| **MVC** | Backend PHP (`app/`) | Separación de Modelo, Vista y Controlador |
| **Repository** | Modelos PHP | Encapsulan toda la lógica de acceso a datos |
| **Dependency Injection** | Controladores y Modelos | PDO inyectado por constructor |
| **Front Controller** | Endpoints `*.php` | Punto de entrada único por endpoint |
| **DTO / Transfer Object** | API JSON | Datos transferidos como objetos JSON estandarizados |
| **Component** | Frontend React | UI como árbol de componentes reutilizables |
| **Container/Presentational** | React Components | Separación entre lógica (ProductList) y presentación (ProductTable, ProductRow) |

---

## 20. Conclusiones

1. **Integración exitosa de módulos:** Los cuatro módulos principales (Autenticación, Productos, POS y Reportes) fueron codificados e integrados satisfactoriamente, compartiendo la capa de datos y comunicándose mediante la API REST.

2. **Arquitectura escalable:** El patrón MVC en el backend y la arquitectura basada en componentes en el frontend permiten extender el sistema con nuevos módulos sin afectar los existentes.

3. **Seguridad robusta:** Se implementaron múltiples capas de seguridad: contraseñas con bcrypt, sentencias preparadas PDO, sanitización de entradas y cabeceras CORS adecuadas.

4. **Compatibilidad dual de base de datos:** El sistema opera correctamente con MySQL (producción) y SQLite (desarrollo), gracias al fallback automático en la capa de configuración.

5. **Calidad de código verificada:** Las 36 pruebas automáticas en Postman con 100% de éxito, junto al uso consistente de buenas prácticas y patrones de diseño, evidencian un desarrollo de calidad profesional.

6. **Control de versiones:** Todo el proceso quedó registrado en el repositorio Git con commits descriptivos usando la convención Conventional Commits, facilitando la trazabilidad del proyecto.

---

## 21. Referencias

- Repositorio GitHub KenkoPOS: https://github.com/Dejosel/kenkopos
- PHP Documentation – PDO: https://www.php.net/manual/es/book.pdo.php
- PHP Documentation – password_hash: https://www.php.net/manual/es/function.password-hash.php
- React Documentation: https://react.dev/
- Vite Documentation: https://vitejs.dev/
- Axios Documentation: https://axios-http.com/
- Bootstrap 5 Documentation: https://getbootstrap.com/docs/5.3/
- Conventional Commits: https://www.conventionalcommits.org/
- SENA – Componente Formativo GA8-220501096

---

*Documento generado como evidencia académica del programa Tecnólogo en Análisis y Desarrollo de Software (ADSO) del SENA.*
*Proyecto KenkoPOS – Repositorio: https://github.com/Dejosel/kenkopos*
*Aprendiz: Jose Luis Hernandez – Agosto 2026*
