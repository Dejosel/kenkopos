/**
 * Pagination
 * 
 * Componente de paginación para navegar entre listas largas.
 * 
 * @param {number} currentPage Página actual
 * @param {number} totalPages Total de páginas
 * @param {function} onPageChange Función a ejecutar al cambiar de página
 */
const Pagination = ({ currentPage, totalPages, onPageChange }) => {
  if (totalPages <= 1) return null;

  const pages = Array.from({ length: totalPages }, (_, i) => i + 1);

  return (
    <nav aria-label="Navegación de páginas">
      <ul className="pagination justify-content-end mb-0">
        <li className={`page-item ${currentPage === 1 ? 'disabled' : ''}`}>
          <button className="page-link" onClick={() => onPageChange(currentPage - 1)}>
            Anterior
          </button>
        </li>
        
        {pages.map(page => (
          <li key={page} className={`page-item ${currentPage === page ? 'active' : ''}`}>
            <button className="page-link" onClick={() => onPageChange(page)}>
              {page}
            </button>
          </li>
        ))}

        <li className={`page-item ${currentPage === totalPages ? 'disabled' : ''}`}>
          <button className="page-link" onClick={() => onPageChange(currentPage + 1)}>
            Siguiente
          </button>
        </li>
      </ul>
    </nav>
  );
};

export default Pagination;
