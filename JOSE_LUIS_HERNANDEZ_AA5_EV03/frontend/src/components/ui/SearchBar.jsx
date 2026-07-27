/**
 * SearchBar
 * 
 * Barra de búsqueda reutilizable.
 * 
 * @param {string} value Valor actual de la búsqueda
 * @param {function} onChange Función que maneja el cambio
 * @param {string} placeholder Texto de ayuda
 */
const SearchBar = ({ value, onChange, placeholder = "Buscar..." }) => {
  return (
    <div className="input-group mb-3 shadow-sm">
      <span className="input-group-text bg-white" id="search-addon">
        🔍
      </span>
      <input
        type="text"
        className="form-control border-start-0"
        placeholder={placeholder}
        aria-label="Search"
        aria-describedby="search-addon"
        value={value}
        onChange={(e) => onChange(e.target.value)}
      />
      {value && (
        <button 
          className="btn btn-outline-secondary bg-white border-start-0 border" 
          type="button"
          onClick={() => onChange('')}
        >
          ✖
        </button>
      )}
    </div>
  );
};

export default SearchBar;
