import axios from 'axios';

/**
 * Servicio API base para consumir el backend de KenkoPOS
 */

// Si estás ejecutando en localhost, apunta a la ruta absoluta del backend.
// Ajusta esto dependiendo de tu entorno local (ej. http://localhost/kenkopos)
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost/kenkopos';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Interceptor para manejar errores globalmente
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Puedes manejar errores globales aquí (ej. si el backend devuelve 401)
    console.error("API Error:", error.response?.data?.message || error.message);
    return Promise.reject(error);
  }
);

export default api;
