# Informe Técnico - KenkoPOS

## 1. Introducción
El proyecto KenkoPOS es un módulo simplificado construido para cumplir con los lineamientos de la evidencia académica SENA GA7-220501096-AA2-EV01. Su objetivo es demostrar habilidades en programación Backend usando PHP y MySQL bajo el paradigma de la Programación Orientada a Objetos.

## 2. Arquitectura y Patrones
- **Arquitectura MVC (Simplificada):** Se dividió la responsabilidad en Modelos (gestión de datos), Controladores (lógica de negocio e intermediación) y Vistas (HTML ubicadas en `public/products/`).
- **Patrón Singleton (Conexión):** La clase `Database` asegura la configuración estructurada del acceso a datos.

## 3. Seguridad Implementada
- **Prevención de Inyección SQL:** Todas las consultas en la clase `Product` se ejecutan mediante *Prepared Statements* (Sentencias Preparadas) proporcionadas por la extensión PDO (`bindParam`).
- **Saneamiento de Salida:** Se utilizó `htmlspecialchars()` en la interfaz visual (Vistas) para prevenir ataques XSS (Cross-Site Scripting).
- **Protección de Eliminación:** La acción de borrar registros está protegida por peticiones HTTP POST (manejado vía JS form submit), para evitar eliminaciones accidentales o maliciosas vía URL GET.

## 4. Requisitos Cumplidos
✅ Programación Orientada a Objetos (POO).
✅ Implementación de CRUD completo.
✅ Conexión usando PDO.
✅ Clases y Controladores independientes.
✅ Uso de plantillas limpias (Bootstrap 5).
✅ Nomenclatura descriptiva en inglés según estándar de industria (PSR).
