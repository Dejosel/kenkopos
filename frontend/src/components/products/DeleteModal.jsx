/**
 * DeleteModal
 * 
 * Modal de confirmación para eliminar un registro.
 * 
 * @param {boolean} show Mostrar u ocultar
 * @param {function} onClose Función para cerrar
 * @param {function} onConfirm Función de confirmación
 * @param {string} itemName Nombre del ítem a eliminar
 * @param {boolean} isDeleting Estado de eliminación
 */
const DeleteModal = ({ show, onClose, onConfirm, itemName, isDeleting }) => {
  if (!show) return null;

  return (
    <div className="modal show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
      <div className="modal-dialog modal-dialog-centered">
        <div className="modal-content shadow">
          <div className="modal-header bg-danger text-white">
            <h5 className="modal-title">Confirmar Eliminación</h5>
            <button 
              type="button" 
              className="btn-close btn-close-white" 
              onClick={onClose}
              disabled={isDeleting}
            ></button>
          </div>
          <div className="modal-body text-center py-4">
            <h1 className="text-danger mb-3">⚠️</h1>
            <p className="fs-5">¿Estás seguro de eliminar el producto?</p>
            <strong className="d-block mb-3">{itemName}</strong>
            <p className="text-muted small">Esta acción no se puede deshacer.</p>
          </div>
          <div className="modal-footer bg-light">
            <button 
              type="button" 
              className="btn btn-secondary" 
              onClick={onClose}
              disabled={isDeleting}
            >
              Cancelar
            </button>
            <button 
              type="button" 
              className="btn btn-danger" 
              onClick={onConfirm}
              disabled={isDeleting}
            >
              {isDeleting ? 'Eliminando...' : 'Sí, Eliminar'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DeleteModal;
