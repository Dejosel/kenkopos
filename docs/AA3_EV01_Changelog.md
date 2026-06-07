# Changelog - KenkoPOS

## [v2.0.0] - 2026-06-07

### Añadido
- Framework Laravel instalado en directorio `laravel-app/`.
- Migración de base de datos para la tabla `products` en Laravel.
- Modelo Eloquent `Product` con fillables y casts.
- `ProductController` como Resource Controller con validaciones de Request.
- Vistas Blade (`index`, `create`, `edit`) heredando de layout principal `app`.
- Documentación para la evidencia GA7-220501096-AA3-EV01 (`docs/AA3_EV01_*.md`).

### Modificado
- Evolución de PHP puro (v1.x) a arquitectura Laravel.
- Reemplazo de protección manual anti-inyección por métodos integrados de Eloquent.
- Formularios actualizados con seguridad CSRF `@csrf` de Laravel.
- Direccionamiento por defecto `/` ahora redirige a `/products`.

---

## [v1.1.0] - 2026-06-04
- (Versión PHP puro: Documentación para evidencia EV02)

## [v1.0.0] - 2026-06-01
- (Versión PHP puro: Entrega inicial EV01)
