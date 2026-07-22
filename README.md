# KenkoPOS API de Autenticación

Este módulo es una API REST desarrollada en PHP 8 y MySQL utilizando PDO, diseñada para el proyecto KenkoPOS. Implementa funcionalidades de registro e inicio de sesión seguro, cumpliendo con la evidencia del SENA **GA7-220501096-AA5-EV01 "Diseño y desarrollo de servicios web"**.

## Tecnologías Utilizadas
- **PHP 8+**: Lenguaje del lado del servidor utilizando programación orientada a objetos (POO).
- **MySQL**: Motor de base de datos.
- **PDO (PHP Data Objects)**: Para una conexión segura a la base de datos y prevención de inyecciones SQL.
- **JSON**: Formato de intercambio de datos en todas las respuestas y peticiones.

## Instalación y Configuración

1. **Clonar o descargar el repositorio.**
2. **Configurar el servidor web:** Colocar la carpeta `kenkopos` en el directorio raíz de tu servidor (ej: `htdocs` en XAMPP, `www` en Laragon).
3. **Importar la Base de Datos:**
   - Abre phpMyAdmin o tu cliente MySQL preferido.
   - Crea una base de datos llamada `kenkopos` (si no existe).
   - Importa el archivo `database/kenkopos.sql`.
4. **Configurar la conexión:**
   - Abre `api/config/config.php` y verifica las credenciales (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`).

## Endpoints

### 1. Registro de Usuario
- **URL**: `/api/register.php`
- **Método HTTP**: `POST`
- **Descripción**: Crea una nueva cuenta de usuario validando datos e insertando en la base de datos con contraseña encriptada.

**Cuerpo de la Petición (JSON):**
```json
{
    "name": "Juan Perez",
    "email": "juan@example.com",
    "password": "mypassword123"
}
```

**Respuesta Exitosa (HTTP 201):**
```json
{
    "success": true,
    "message": "Usuario registrado correctamente"
}
```

### 2. Inicio de Sesión
- **URL**: `/api/login.php`
- **Método HTTP**: `POST`
- **Descripción**: Autentica a un usuario verificando su correo electrónico y contraseña.

**Cuerpo de la Petición (JSON):**
```json
{
    "email": "juan@example.com",
    "password": "mypassword123"
}
```

**Respuesta Exitosa (HTTP 200):**
```json
{
    "success": true,
    "message": "Autenticación satisfactoria",
    "user": {
        "id": 1,
        "name": "Juan Perez"
    }
}
```

## Pruebas con Postman
En la raíz del proyecto se incluye el archivo `KenkoPOS.postman_collection.json`. 
1. Abre Postman.
2. Selecciona `Import` y carga este archivo.
3. Esto importará una colección con las peticiones preconfiguradas para probar los endpoints de Registro y Login fácilmente.
