import ProductRow from './ProductRow';

/**
 * ProductTable
 * 
 * Tabla que lista los productos.
 * 
 * @param {array} products Lista de productos
 * @param {function} onEdit Función para editar
 * @param {function} onDelete Función para eliminar
 */
const ProductTable = ({ products, onEdit, onDelete }) => {
  return (
    <div className="table-responsive bg-white rounded shadow-sm border">
      <table className="table table-hover table-striped mb-0 align-middle">
        <thead className="table-light">
          <tr>
            <th scope="col">SKU</th>
            <th scope="col">Nombre</th>
            <th scope="col">Categoría</th>
            <th scope="col">Color</th>
            <th scope="col" className="text-end">Precio</th>
            <th scope="col" className="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          {products.map(product => (
            <ProductRow 
              key={product.product_id} 
              product={product} 
              onEdit={onEdit}
              onDelete={onDelete}
            />
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default ProductTable;
