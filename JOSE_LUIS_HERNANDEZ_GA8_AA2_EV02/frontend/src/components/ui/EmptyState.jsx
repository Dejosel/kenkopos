/**
 * EmptyState
 * 
 * Componente que se muestra cuando no hay datos para listar.
 * 
 * @param {string} title Título principal
 * @param {string} description Descripción adicional
 * @param {ReactNode} action Botón de acción opcional (ej: Crear producto)
 */
const EmptyState = ({ title = "No hay datos", description = "No se encontraron registros.", action }) => {
  return (
    <div className="text-center py-5 bg-white rounded shadow-sm border">
      <div className="mb-3">
        <span className="display-4 text-muted">📁</span>
      </div>
      <h4 className="text-secondary">{title}</h4>
      <p className="text-muted">{description}</p>
      {action && <div className="mt-3">{action}</div>}
    </div>
  );
};

export default EmptyState;
