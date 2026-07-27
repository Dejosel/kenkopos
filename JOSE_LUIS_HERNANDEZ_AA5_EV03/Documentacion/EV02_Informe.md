# Informe Técnico - GA7-220501096-AA2-EV02
## Módulos de Software Codificados y Probados

**Proyecto:** KenkoPOS  
**Repositorio:** https://github.com/Dejosel/kenkopos  
**Versión:** v1.1.0  
**Fecha:** Junio 2026  
**Aprendiz:** Dejosel  
**Evidencia:** GA7-220501096-AA2-EV02

---

## 1. Introducción

El presente informe corresponde a la evidencia GA7-220501096-AA2-EV02 del programa de formación SENA, en el cual se verifica que los módulos de software del proyecto KenkoPOS han sido correctamente **codificados y probados**.

KenkoPOS es un módulo demostrativo de gestión de productos desarrollado en PHP 8 y MySQL, tomado como base de la evidencia anterior (EV01 - v1.0.0). En esta entrega se documenta la verificación de los módulos implementados, los formularios HTML utilizados, el uso de los métodos HTTP GET y POST, la conexión a la base de datos, y se proponen y registran pruebas funcionales de cada módulo.

---

## 2. Objetivo General

Verificar y documentar que los módulos del proyecto KenkoPOS se encuentran correctamente codificados según los requerimientos establecidos en la EV01, y que han sido sometidos a un proceso de pruebas funcionales que valide su correcto funcionamiento.

---

## 3. Tecnologías Utilizadas

| Tecnología | Versión | Rol en el proyecto |
|---|---|---|
| PHP | 8.x | Lenguaje de backend y lógica de negocio |
| MySQL | 5.7+ | Motor de base de datos relacional |
| PDO | Nativa PHP 8 | Extensión para conexión segura a MySQL |
| HTML5 | 5 | Estructura de formularios y vistas |
| Bootstrap | 5.3.0 | Estilos e interfaz de usuario |
| JavaScript | ES6 | Confirmaciones de acciones en el cliente |
| Git | 2.x | Control de versiones |

---

## 4. Módulos Implementados

El proyecto KenkoPOS implementa un único módulo principal denominado **Módulo de Gestión de Productos**, el cual está compuesto por las siguientes operaciones CRUD:

| Módulo | Archivo Vista | Clase Controlador | Clase Modelo | Operación BD |
|---|---|---|---|---|
| Listar Productos | `public/products/list.php` | `ProductController::index()` | `Product::readAll()` | SELECT |
| Crear Producto | `public/products/create.php` | `ProductController::store()` | `Product::create()` | INSERT |
| Editar Producto | `public/products/edit.php` | `ProductController::update()` | `Product::update()` | UPDATE |
| Eliminar Producto | `public/products/delete.php` | `ProductController::destroy()` | `Product::delete()` | DELETE |
| Consultar por ID | (usado internamente por edit.php) | `ProductController::show()` | `Product::readById()` | SELECT WHERE |

### Clases implementadas

- **`Config\Database`** — `config/database.php`: Gestiona la conexión PDO a MySQL.
- **`App\Models\Product`** — `app/Models/Product.php`: Interactúa con la tabla `products` (CRUD).
- **`App\Controllers\ProductController`** — `app/Controllers/ProductController.php`: Coordina las peticiones entre vistas y modelo.
- **`App\Helpers\Response`** — `app/Helpers/Response.php`: Provee método estático de redirección HTTP.

---

## 5. Formularios HTML Identificados

Se identificaron **dos formularios HTML** en el proyecto:

### Formulario 1 — Crear Producto
- **Archivo:** `public/products/create.php` (línea 31)
- **Método:** `POST`
- **Acción:** `create.php`
- **Campos:**
  - `sku` — Texto, requerido, máximo 50 caracteres
  - `name` — Texto, requerido, máximo 255 caracteres
  - `price` — Número decimal (step 0.01), requerido

### Formulario 2 — Editar Producto
- **Archivo:** `public/products/edit.php` (línea 45)
- **Método:** `POST`
- **Acción:** `edit.php?id={product_id}`
- **Campos:**
  - `product_id` — Hidden (campo oculto con el ID del producto)
  - `sku` — Texto pre-cargado, requerido, máximo 50 caracteres
  - `name` — Texto pre-cargado, requerido, máximo 255 caracteres
  - `price` — Número decimal pre-cargado, requerido

---

## 6. Uso del Método GET

El método GET se utiliza en los siguientes casos del proyecto:

| Archivo | Línea aprox. | Uso de GET |
|---|---|---|
| `edit.php` | L12 | `$_GET['id']` — Recibe el ID del producto para cargar en el formulario |
| `edit.php` | L41 | `$_GET['error']` — Muestra alerta de error en el formulario de edición |
| `create.php` | L27 | `$_GET['error']` — Muestra alerta de error en el formulario de creación |
| `list.php` | L24 | `$_GET['msg']` — Muestra alerta de éxito tras operación |
| `list.php` | L52 | Enlace `edit.php?id=...` — Pasa el ID como parámetro GET |

**Ejemplo de código (edit.php):**
```php
$id = $_GET['id'] ?? null;
$product = $controller->show((int)$id);
```

---

## 7. Uso del Método POST

El método POST se utiliza para todas las operaciones de escritura en la base de datos:

| Archivo | Operación | Campos enviados por POST |
|---|---|---|
| `create.php` | Crear producto | `name`, `sku`, `price` |
| `edit.php` | Actualizar producto | `product_id`, `name`, `sku`, `price` |
| `delete.php` | Eliminar producto | `product_id` (enviado via JS) |

**Ejemplo de código (create.php):**
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller = new ProductController();
    $controller->store($_POST);
}
```

**Protección de eliminación mediante POST (app.js):**
```javascript
const form = document.createElement('form');
form.method = 'POST';
form.action = 'delete.php';
// ... envía product_id como campo oculto
```

---

## 8. Conexión a MySQL mediante PDO

La conexión está implementada en `config/database.php` mediante la clase `Database`:

```php
$dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
$this->conn = new PDO($dsn, $this->username, $this->password);
$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
```

**Características de la conexión:**
- Protocolo: PDO (PHP Data Objects)
- Motor: MySQL
- Charset: UTF-8 MB4 (soporte completo Unicode)
- Modo de error: `ERRMODE_EXCEPTION` (lanza excepciones ante fallos)
- Modo de fetch: `FETCH_ASSOC` (retorna arrays asociativos)
- **Prepared Statements:** Todas las consultas usan `bindParam()` para prevenir inyección SQL

---

## 9. Uso de Git

El proyecto utiliza Git como sistema de control de versiones con repositorio remoto en GitHub:

| Elemento | Detalle |
|---|---|
| Plataforma | GitHub |
| Repositorio | https://github.com/Dejosel/kenkopos |
| Rama principal | `main` |
| Versión anterior | `v1.0.0` |
| Versión actual | `v1.1.0` |
| Archivo `.gitignore` | Excluye `/vendor/`, `.env`, `.DS_Store` |

---

## 10. Conclusiones

- Los módulos del proyecto KenkoPOS se encuentran correctamente **codificados** en PHP 8, siguiendo el patrón MVC simplificado y estándares PSR.
- Se verificó la existencia de **dos formularios HTML** funcionales (crear y editar), con sus respectivas validaciones de campos requeridos.
- Se documentó el uso correcto de los métodos **HTTP GET** (para recuperar IDs y mensajes de estado) y **HTTP POST** (para envío seguro de datos de escritura).
- La **conexión a MySQL** se realiza mediante PDO con prepared statements, garantizando seguridad contra inyección SQL.
- El proyecto cuenta con **control de versiones Git** y repositorio público en GitHub.
- La documentación generada en esta versión v1.1.0 complementa los artefactos de la EV01, cumpliendo con los requisitos de la evidencia GA7-220501096-AA2-EV02.
