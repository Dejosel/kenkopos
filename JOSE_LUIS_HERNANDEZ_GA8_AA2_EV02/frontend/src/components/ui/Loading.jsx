/**
 * Loading
 * 
 * Componente que muestra un spinner de Bootstrap 5
 * mientras se cargan los datos.
 */
const Loading = () => {
  return (
    <div className="d-flex justify-content-center align-items-center py-5">
      <div className="spinner-border text-primary" role="status">
        <span className="visually-hidden">Cargando...</span>
      </div>
    </div>
  );
};

export default Loading;
