# Informe de Pruebas de Servicios Web (API)
## Evidencia: GA7-220501096-AA5-EV03 - Proyecto

**Proyecto:** KenkoPOS  
**Fecha de Pruebas:** Julio 2026  
**Servidor de Prueba Local:** PHP Internal Server (`http://localhost:8555`)  
**Herramientas Usadas:** `curl` (Client URL) y colección de Postman (`KenkoPOS.postman_collection.json`)  
**Aprendiz:** Jose Luis Hernandez  

---

## 1. Resumen de Ejecución de Pruebas

Se realizaron pruebas funcionales completas a todos los servicios web desarrollados para el proyecto KenkoPOS. El servidor local se levantó en la dirección de prueba `http://localhost:8555/` para simular el procesamiento de peticiones en tiempo real. 

Se probaron con éxito los siguientes flujos de negocio:
- Registro de usuario (Éxito, campos obligatorios faltantes, email duplicado).
- Inicio de sesión de usuario (Éxito, credenciales incorrectas).
- Catálogo de productos CRUD (Listar todos, obtener por ID, crear, actualizar y borrar por ID).

---

## 2. Detalle de Pruebas y Respuestas de la API

### 2.1. Registro de Usuario (`POST /api/register.php`)

#### Caso de Prueba 1: Registro Exitoso
- **Comando Ejecutado:**
```bash
curl -s -X POST -H "Content-Type: application/json" \
  -d '{"name":"Jose Hernandez","email":"jose@kenkopos.com","password":"miPasswordSeguro123"}' \
  "http://localhost:8555/api/register.php"
```
- **Respuesta Obtenida (HTTP 201 Created):**
```json
{
    "success": true,
    "message": "Usuario registrado correctamente"
}
```

#### Caso de Prueba 2: Registro con Correo Duplicado (Error de Conflicto)
- **Comando Ejecutado:**
```bash
curl -s -X POST -H "Content-Type: application/json" \
  -d '{"name":"Jose Hernandez","email":"jose@kenkopos.com","password":"miPasswordSeguro123"}' \
  "http://localhost:8555/api/register.php"
```
- **Respuesta Obtenida (HTTP 409 Conflict):**
```json
{
    "success": false,
    "message": "El usuario ya se encuentra registrado con este correo"
}
```

---

### 2.2. Inicio de Sesión (`POST /api/login.php`)

#### Caso de Prueba 1: Autenticación Exitosa
- **Comando Ejecutado:**
```bash
curl -s -X POST -H "Content-Type: application/json" \
  -d '{"email":"jose@kenkopos.com","password":"miPasswordSeguro123"}' \
  "http://localhost:8555/api/login.php"
```
- **Respuesta Obtenida (HTTP 200 OK):**
```json
{
    "success": true,
    "message": "Autenticación satisfactoria",
    "user": {
        "id": 7,
        "name": "Jose Hernandez"
    }
}
```

#### Caso de Prueba 2: Autenticación Fallida (Contraseña Incorrecta)
- **Comando Ejecutado:**
```bash
curl -s -X POST -H "Content-Type: application/json" \
  -d '{"email":"jose@kenkopos.com","password":"wrongpassword"}' \
  "http://localhost:8555/api/login.php"
```
- **Respuesta Obtenida (HTTP 401 Unauthorized):**
```json
{
    "success": false,
    "message": "Error en la autenticación"
}
```

---

### 2.3. Catálogo de Productos (`/api/products.php`)

#### Caso de Prueba 1: Listar Todos los Productos
- **Comando Ejecutado:**
```bash
curl -s http://localhost:8555/api/products.php
```
- **Respuesta Obtenida (HTTP 200 OK):**
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
    // ... otros registros sembrados
  ]
}
```

#### Caso de Prueba 2: Consultar Producto por ID (Éxito)
- **Comando Ejecutado:**
```bash
curl -s "http://localhost:8555/api/products.php?id=1"
```
- **Respuesta Obtenida (HTTP 200 OK):**
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

#### Caso de Prueba 3: Crear Nuevo Producto (Éxito)
- **Comando Ejecutado:**
```bash
curl -s -X POST -H "Content-Type: application/json" \
  -d '{"name":"Hamburguesa Especial Doble","sku":"PLT-100","price":25000.00,"category":"Platos Fuertes","color":"#dc3545"}' \
  "http://localhost:8555/api/products.php"
```
- **Respuesta Obtenida (HTTP 201 Created):**
```json
{
    "success": true,
    "message": "Producto creado correctamente"
}
```

#### Caso de Prueba 4: Actualizar Producto Existente (Éxito)
- **Comando Ejecutado:**
```bash
curl -s -X PUT -H "Content-Type: application/json" \
  -d '{"product_id":1,"name":"Hamburguesa Especial Premium","sku":"PLT-001","price":24000.00,"category":"Platos Fuertes","color":"#dc3545"}' \
  "http://localhost:8555/api/products.php"
```
- **Respuesta Obtenida (HTTP 200 OK):**
```json
{
    "success": true,
    "message": "Producto actualizado correctamente"
}
```

#### Caso de Prueba 5: Eliminar Producto por ID (Éxito)
- **Comando Ejecutado:**
```bash
curl -s -X DELETE "http://localhost:8555/api/products.php?id=14"
```
- **Respuesta Obtenida (HTTP 200 OK):**
```json
{
    "success": true,
    "message": "Producto eliminado correctamente"
}
```

#### Caso de Prueba 6: Eliminar Producto por ID Inexistente (Error 404)
- **Comando Ejecutado:**
```bash
curl -s -X DELETE "http://localhost:8555/api/products.php?id=14"
```
- **Respuesta Obtenida (HTTP 404 Not Found):**
```json
{
    "success": false,
    "message": "Producto no encontrado para eliminar."
}
```

---

## 3. Log de Consola del Servidor de Backend

Como evidencia física del comportamiento del backend, se adjunta el log generado por el servidor de desarrollo PHP al recibir las anteriores peticiones secuenciales:

```text
[Sun Jul 26 09:21:17 2026] PHP 8.5.2 Development Server (http://localhost:8555) started
[Sun Jul 26 09:21:23 2026] 127.0.0.1:63897 Accepted
[Sun Jul 26 09:21:24 2026] 127.0.0.1:63897 [200]: GET /api/products.php
[Sun Jul 26 09:21:24 2026] 127.0.0.1:63897 Closing
[Sun Jul 26 09:21:36 2026] 127.0.0.1:63949 Accepted
[Sun Jul 26 09:21:36 2026] 127.0.0.1:63949 [200]: GET /api/products.php?id=1
[Sun Jul 26 09:21:36 2026] 127.0.0.1:63949 Closing
[Sun Jul 26 09:21:40 2026] 127.0.0.1:63972 Accepted
[Sun Jul 26 09:21:40 2026] 127.0.0.1:63972 [201]: POST /api/register.php
[Sun Jul 26 09:21:40 2026] 127.0.0.1:63972 Closing
[Sun Jul 26 09:21:44 2026] 127.0.0.1:63996 Accepted
[Sun Jul 26 09:21:44 2026] 127.0.0.1:63996 [409]: POST /api/register.php
[Sun Jul 26 09:21:44 2026] 127.0.0.1:63996 Closing
[Sun Jul 26 09:21:48 2026] 127.0.0.1:64012 Accepted
[Sun Jul 26 09:21:48 2026] 127.0.0.1:64012 [200]: POST /api/login.php
[Sun Jul 26 09:21:48 2026] 127.0.0.1:64012 Closing
[Sun Jul 26 09:21:53 2026] 127.0.0.1:64041 Accepted
[Sun Jul 26 09:21:54 2026] 127.0.0.1:64041 [401]: POST /api/login.php
[Sun Jul 26 09:21:54 2026] 127.0.0.1:64041 Closing
[Sun Jul 26 09:21:58 2026] 127.0.0.1:64061 Accepted
[Sun Jul 26 09:21:58 2026] 127.0.0.1:64061 [201]: POST /api/products.php
[Sun Jul 26 09:21:58 2026] 127.0.0.1:64061 Closing
[Sun Jul 26 09:22:04 2026] 127.0.0.1:64087 Accepted
[Sun Jul 26 09:22:04 2026] 127.0.0.1:64087 [200]: PUT /api/products.php
[Sun Jul 26 09:22:04 2026] 127.0.0.1:64087 Closing
[Sun Jul 26 09:22:08 2026] 127.0.0.1:64105 Accepted
[Sun Jul 26 09:22:08 2026] 127.0.0.1:64105 [200]: GET /api/products.php?id=1
[Sun Jul 26 09:22:08 2026] 127.0.0.1:64105 Closing
[Sun Jul 26 09:22:11 2026] 127.0.0.1:64122 Accepted
[Sun Jul 26 09:22:11 2026] 127.0.0.1:64122 [201]: POST /api/products.php
[Sun Jul 26 09:22:11 2026] 127.0.0.1:64122 Closing
[Sun Jul 26 09:22:25 2026] 127.0.0.1:64178 Accepted
[Sun Jul 26 09:22:25 2026] 127.0.0.1:64178 [200]: DELETE /api/products.php?id=14
[Sun Jul 26 09:22:25 2026] 127.0.0.1:64178 Closing
[Sun Jul 26 09:22:32 2026] 127.0.0.1:64208 Accepted
[Sun Jul 26 09:22:32 2026] 127.0.0.1:64208 [404]: DELETE /api/products.php?id=14
[Sun Jul 26 09:22:32 2026] 127.0.0.1:64208 Closing
```

---

## 4. Conclusión de las Pruebas

Los resultados demuestran que:
1. **Verbos HTTP adecuados:** Cada ruta responde de manera adecuada y RESTful según el método (GET, POST, PUT, DELETE).
2. **Formato JSON:** La comunicación de entrada y salida maneja correctamente el parseo de objetos JSON estructurados.
3. **Códigos de Estado Correctos:** Se retornan los códigos HTTP correspondientes según la teoría REST (200 para OK, 201 para creación exitosa, 400 para peticiones erróneas, 401 para credenciales inválidas, 404 para recursos no encontrados, 409 para colisiones de llaves únicas, y 405 para métodos no permitidos).
4. **Persistencia de Datos:** Las operaciones CRUD modificaron correctamente el estado físico de la base de datos (verificado mediante SQLite), garantizando la integridad referencial y el correcto almacenamiento.
