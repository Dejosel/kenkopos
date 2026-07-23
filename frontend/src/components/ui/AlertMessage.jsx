import { useEffect } from 'react';

/**
 * AlertMessage
 * 
 * Componente para mostrar mensajes de éxito o error.
 * Se oculta automáticamente después de 5 segundos.
 * 
 * @param {string} type Tipo de alerta (success, danger, warning, info)
 * @param {string} message Mensaje a mostrar
 * @param {function} onClose Función a ejecutar al cerrar la alerta
 */
const AlertMessage = ({ type = 'info', message, onClose }) => {
  useEffect(() => {
    if (message) {
      const timer = setTimeout(() => {
        if (onClose) onClose();
      }, 5000); // 5 segundos

      return () => clearTimeout(timer);
    }
  }, [message, onClose]);

  if (!message) return null;

  return (
    <div className={`alert alert-${type} alert-dismissible fade show shadow-sm`} role="alert">
      {message}
      <button 
        type="button" 
        className="btn-close" 
        data-bs-dismiss="alert" 
        aria-label="Close"
        onClick={onClose}
      ></button>
    </div>
  );
};

export default AlertMessage;
