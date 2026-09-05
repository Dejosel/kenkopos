import ProductForm from './ProductForm';

/**
 * ProductModal
 * 
 * Modal de Bootstrap 5 para crear o editar un producto.
 * 
 * @param {boolean} show Mostrar u ocultar el modal
 * @param {function} onClose Función para cerrar el modal
 * @param {object} initialData Datos si se está editando
 * @param {function} onSubmit Envío del formulario
 * @param {boolean} isSubmitting Estado de envío
 */
const ProductModal = ({ show, onClose, initialData, onSubmit, isSubmitting }) => {
  // Cuando el modal está cerrado, no renderizamos el fondo oscuro
  if (!show) return null;

  return (
    <div className="modal show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
      <div className="modal-dialog modal-dialog-centered">
        <div className="modal-content shadow">
          <div className="modal-header bg-light">
            <h5 className="modal-title">
              {initialData ? 'Editar Producto' : 'Nuevo Producto'}
            </h5>
            <button 
              type="button" 
              className="btn-close" 
              onClick={onClose}
              disabled={isSubmitting}
            ></button>
          </div>
          <div className="modal-body">
            <ProductForm 
              initialData={initialData} 
              onSubmit={onSubmit} 
              isSubmitting={isSubmitting}
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductModal;
