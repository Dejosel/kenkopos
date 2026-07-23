import { Link } from 'react-router-dom';

/**
 * Navbar
 * 
 * Barra de navegación superior de la aplicación.
 */
const Navbar = () => {
  return (
    <nav className="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
      <div className="container-fluid">
        <Link className="navbar-brand fw-bold" to="/">
          🍣 KenkoPOS React
        </Link>
        <button 
          className="navbar-toggler" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#navbarNav"
        >
          <span className="navbar-toggler-icon"></span>
        </button>
        <div className="collapse navbar-collapse" id="navbarNav">
          <ul className="navbar-nav ms-auto">
            <li className="nav-item">
              <span className="nav-link text-white">
                Admin User
              </span>
            </li>
            <li className="nav-item">
              <a className="nav-link text-white-50" href="#">Salir</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
