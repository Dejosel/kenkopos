# KenkoPOS React Frontend

Este proyecto corresponde a la evidencia **GA7-220501096-AA4-EV03** del SENA ("Componente frontend del proyecto formativo y proyectos de clase").

Se trata del frontend de KenkoPOS desarrollado en React 19, consumiendo el backend PHP y base de datos MySQL existentes.

## Tecnologías

- **React 19**
- **Vite**
- **React Router DOM** (Enrutamiento)
- **Axios** (Peticiones HTTP)
- **Bootstrap 5** (Estilos y componentes UI)

## Descripción

La aplicación es un Single Page Application (SPA) que permite realizar el CRUD completo del módulo de Productos. Mantiene una arquitectura escalable, utilizando Hooks (`useState`, `useEffect`), y componentes organizados por responsabilidades (Layouts, UI, Products, Services).

## Configuración e Instalación

### Requisitos previos
- Node.js (v18+)
- Backend de KenkoPOS en ejecución (Apache/MySQL)

### Instalación

1. Clona el repositorio e ingresa a la carpeta del frontend:
   ```bash
   cd kenkopos/frontend
   ```

2. Instala las dependencias:
   ```bash
   npm install
   ```

3. (Opcional) Configura la URL de la API:
   Abre `src/services/api.js` y asegúrate de que la constante `API_URL` apunte a la ruta donde se expone `api/products.php`. Por defecto es `http://localhost/kenkopos`.

### Ejecución

Inicia el servidor de desarrollo local de Vite:

```bash
npm run dev
```

La consola te indicará en qué puerto local está corriendo la aplicación (usualmente `http://localhost:5173/`).

## Características Cumplidas (Evidencia)

- ✔ React Hooks implementados para estado y efectos secundarios.
- ✔ React Router para navegación sin recargas.
- ✔ Consumo de API RESTful mediante Axios.
- ✔ Módulo `api/products.php` para adaptar el MVC actual a REST.
- ✔ Formularios con validación local exhaustiva.
- ✔ Bootstrap 5 para un diseño responsivo y moderno (Modales, Tablas, Alertas).
- ✔ Completamente comentado en español.
- ✔ Arquitectura Frontend moderna separada del Backend.

## Autor

Desarrollador SENA
[Enlace al Repositorio]
