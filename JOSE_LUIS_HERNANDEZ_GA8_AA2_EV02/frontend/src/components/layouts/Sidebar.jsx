import { NavLink } from 'react-router-dom';

/**
 * Sidebar
 * 
 * Menú lateral de navegación.
 */
const Sidebar = () => {
  return (
    <nav className="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse border-end shadow-sm" id="sidebarMenu">
      <div className="position-sticky pt-3">
        <ul className="nav flex-column gap-2 p-2">
          <li className="nav-item">
            <NavLink 
              className={({ isActive }) => `nav-link rounded ${isActive ? 'active bg-primary text-white' : 'text-dark hover-bg-light'}`}
              to="/"
            >
              📦 Productos
            </NavLink>
          </li>
          {/* Aquí podrían ir más enlaces si la app crece */}
          <li className="nav-item">
            <span className="nav-link text-muted" style={{cursor: 'not-allowed'}}>
              🛒 Ventas (Próximamente)
            </span>
          </li>
        </ul>
      </div>
    </nav>
  );
};

export default Sidebar;
