import { useState, useEffect } from 'react';
import api from '../../services/api';
import ProductTable from './ProductTable';
import ProductModal from './ProductModal';
import DeleteModal from './DeleteModal';
import SearchBar from '../ui/SearchBar';
import Pagination from '../ui/Pagination';
import Loading from '../ui/Loading';
import AlertMessage from '../ui/AlertMessage';
import EmptyState from '../ui/EmptyState';

/**
 * ProductList
 * 
 * Componente contenedor principal para la gestión de productos.
 * Controla el estado, peticiones HTTP (Axios) y lógica de negocio.
 */
const ProductList = () => {
  // Estados de datos
  const [products, setProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  
  // Estados UI
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [alert, setAlert] = useState({ show: false, message: '', type: 'info' });
  
  // Estados de Modales
  const [showProductModal, setShowProductModal] = useState(false);
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [currentProduct, setCurrentProduct] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Estados de paginación
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 10;

  // Cargar productos al montar
  useEffect(() => {
    fetchProducts();
  }, []);

  // Filtrar productos cuando cambia el término de búsqueda o la lista
  useEffect(() => {
    let result = products;
    if (searchTerm) {
      const term = searchTerm.toLowerCase();
      result = products.filter(p => 
        p.name.toLowerCase().includes(term) || 
        p.sku.toLowerCase().includes(term) ||
        p.category.toLowerCase().includes(term)
      );
    }
    setFilteredProducts(result);
    setCurrentPage(1); // Reset a primera página al buscar
  }, [searchTerm, products]);

  // Función para obtener productos del API
  const fetchProducts = async () => {
    try {
      setLoading(true);
      const response = await api.get('/api/products.php');
      if (response.data.success) {
        setProducts(response.data.products);
      }
    } catch (error) {
      showAlert('Error al cargar los productos', 'danger');
    } finally {
      setLoading(false);
    }
  };

  const showAlert = (message, type = 'info') => {
    setAlert({ show: true, message, type });
  };

  // Manejadores de Modales
  const handleCreateNew = () => {
    setCurrentProduct(null);
    setShowProductModal(true);
  };

  const handleEdit = (product) => {
    setCurrentProduct(product);
    setShowProductModal(true);
  };

  const handleDeleteRequest = (product) => {
    setCurrentProduct(product);
    setShowDeleteModal(true);
  };

  // Peticiones de escritura (POST/PUT)
  const handleSaveProduct = async (formData) => {
    setIsSubmitting(true);
    try {
      if (currentProduct) {
        // Actualizar
        const payload = { ...formData, product_id: currentProduct.product_id };
        const res = await api.put('/api/products.php', payload);
        if (res.data.success) {
          showAlert('Producto actualizado con éxito', 'success');
        }
      } else {
        // Crear
        const res = await api.post('/api/products.php', formData);
        if (res.data.success) {
          showAlert('Producto creado con éxito', 'success');
        }
      }
      setShowProductModal(false);
      fetchProducts(); // Recargar lista
    } catch (error) {
      showAlert(error.response?.data?.message || 'Error al guardar el producto', 'danger');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Petición de eliminación (DELETE)
  const handleConfirmDelete = async () => {
    setIsSubmitting(true);
    try {
      const res = await api.delete(`/api/products.php?id=${currentProduct.product_id}`);
      if (res.data.success) {
        showAlert('Producto eliminado con éxito', 'success');
        setShowDeleteModal(false);
        fetchProducts(); // Recargar
      }
    } catch (error) {
      showAlert(error.response?.data?.message || 'Error al eliminar', 'danger');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Paginación lógica
  const indexOfLastItem = currentPage * itemsPerPage;
  const indexOfFirstItem = indexOfLastItem - itemsPerPage;
  const currentItems = filteredProducts.slice(indexOfFirstItem, indexOfLastItem);
  const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2 className="mb-0">Gestión de Productos</h2>
        <button className="btn btn-primary shadow-sm" onClick={handleCreateNew}>
          ➕ Nuevo Producto
        </button>
      </div>

      <AlertMessage 
        show={alert.show} 
        message={alert.message} 
        type={alert.type} 
        onClose={() => setAlert({ show: false, message: '' })} 
      />

      <div className="row mb-3">
        <div className="col-md-6">
          <SearchBar 
            value={searchTerm} 
            onChange={setSearchTerm} 
            placeholder="Buscar por nombre, SKU o categoría..."
          />
        </div>
      </div>

      {loading ? (
        <Loading />
      ) : filteredProducts.length > 0 ? (
        <>
          <ProductTable 
            products={currentItems} 
            onEdit={handleEdit} 
            onDelete={handleDeleteRequest} 
          />
          <div className="mt-4">
            <Pagination 
              currentPage={currentPage} 
              totalPages={totalPages} 
              onPageChange={setCurrentPage} 
            />
          </div>
        </>
      ) : (
        <EmptyState 
          title={searchTerm ? "No se encontraron resultados" : "No hay productos registrados"}
          description={searchTerm ? "Prueba con otra palabra clave." : "Comienza agregando tu primer producto al sistema."}
          action={!searchTerm && (
            <button className="btn btn-outline-primary" onClick={handleCreateNew}>
              Crear el primer producto
            </button>
          )}
        />
      )}

      {/* Modales */}
      <ProductModal 
        show={showProductModal}
        onClose={() => setShowProductModal(false)}
        initialData={currentProduct}
        onSubmit={handleSaveProduct}
        isSubmitting={isSubmitting}
      />

      <DeleteModal 
        show={showDeleteModal}
        onClose={() => setShowDeleteModal(false)}
        onConfirm={handleConfirmDelete}
        itemName={currentProduct?.name}
        isDeleting={isSubmitting}
      />
    </div>
  );
};

export default ProductList;
