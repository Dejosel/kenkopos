/**
 * ProductRow
 * 
 * Fila individual de la tabla de productos.
 * 
 * @param {object} product Datos del producto
 * @param {function} onEdit Función para editar
 * @param {function} onDelete Función para eliminar
 */
const ProductRow = ({ product, onEdit, onDelete }) => {
  // Formatear precio a moneda local (COP o USD según prefieras)
  const formattedPrice = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(product.price);

  return (
    <tr>
      <td className="align-middle fw-bold">{product.sku}</td>
      <td className="align-middle">{product.name}</td>
      <td className="align-middle">
        <span className="badge bg-secondary">{product.category}</span>
      </td>
      <td className="align-middle">{product.color || '-'}</td>
      <td className="align-middle font-monospace text-end">{formattedPrice}</td>
      <td className="align-middle text-center">
        <div className="btn-group" role="group">
          <button 
            type="button" 
            className="btn btn-sm btn-outline-primary"
            onClick={() => onEdit(product)}
            title="Editar"
          >
            ✏️
          </button>
          <button 
            type="button" 
            className="btn btn-sm btn-outline-danger"
            onClick={() => onDelete(product)}
            title="Eliminar"
          >
            🗑️
          </button>
        </div>
      </td>
    </tr>
  );
};

export default ProductRow;
