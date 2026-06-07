# Estándares Aplicados en KenkoPOS (Laravel)

## 1. Patrón MVC
La refactorización mantiene e intensifica el patrón MVC gracias al framework:
- **Modelos**: Carpeta `app/Models/` (interacción DB vía Eloquent).
- **Vistas**: Carpeta `resources/views/` (plantillas Blade compiladas).
- **Controladores**: Carpeta `app/Http/Controllers/` (lógica de petición y validación).

## 2. Estándares PSR
Laravel 11+ sigue de manera estricta los estándares de interoperabilidad PSR-4 para autocarga de clases (Composer) y PSR-12 para estilo de código.

## 3. Seguridad
- Protección CSRF: Cada formulario utiliza `@csrf` impidiendo peticiones cruzadas falsificadas.
- Saneamiento Automático: Las plantillas Blade escapan automáticamente la salida `{{ $var }}`, eliminando vulnerabilidades XSS.
- Inyección SQL: Eloquent utiliza Prepared Statements PDO por detrás por defecto.

## 4. RESTful Routing
Se aplicó el estándar REST al nombramiento de las rutas mediante `Route::resource()`, lo que estandariza los endpoints HTTP.
