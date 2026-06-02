# KenkoPOS - Módulo de Gestión de Productos

Proyecto académico desarrollado para cumplir con la evidencia SENA: **GA7-220501096-AA2-EV01 - Codificación de módulos del software según requerimientos del proyecto**.

## Objetivo
Este proyecto es un módulo demostrativo (no un POS completo) para la gestión de productos, implementando operaciones CRUD (Crear, Consultar, Actualizar, Eliminar) mediante PHP 8, MySQL y POO, con una interfaz basada en HTML5 y Bootstrap 5.

## Tecnologías Utilizadas
- **Backend:** PHP 8
- **Base de Datos:** MySQL (con extensión PDO)
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Control de Versiones:** Git

## Instalación y Configuración
1. Clonar el repositorio.
2. Importar el script SQL ubicado en `database/kenkopos.sql` en tu servidor MySQL.
3. Configurar las credenciales de conexión en `config/database.php`.
4. Levantar un servidor local (ej. XAMPP, Laragon, o PHP Built-in Server `php -S localhost:8000 -t public/`).
5. Acceder a la ruta pública (`/public/index.php`) desde el navegador.
