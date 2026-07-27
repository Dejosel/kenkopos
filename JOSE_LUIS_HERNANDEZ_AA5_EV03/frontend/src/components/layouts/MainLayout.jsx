import { Outlet } from 'react-router-dom';
import Navbar from './Navbar';
import Sidebar from './Sidebar';
import Footer from './Footer';

/**
 * MainLayout
 * 
 * Componente contenedor que estructura la interfaz principal,
 * incluyendo barra de navegación, menú lateral y pie de página.
 */
const MainLayout = () => {
  return (
    <div className="d-flex flex-column min-vh-100">
      <Navbar />
      
      <div className="container-fluid flex-grow-1 d-flex p-0">
        <Sidebar />
        
        <main className="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 w-100 bg-light">
          {/* El contenido de la página actual se renderiza aquí */}
          <Outlet />
        </main>
      </div>

      <Footer />
    </div>
  );
};

export default MainLayout;
