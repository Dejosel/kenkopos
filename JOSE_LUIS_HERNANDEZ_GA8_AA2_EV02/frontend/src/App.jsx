import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import MainLayout from './components/layouts/MainLayout';
import ProductsPage from './pages/ProductsPage';

/**
 * App
 * 
 * Componente raíz que configura React Router DOM.
 */
function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<MainLayout />}>
          <Route index element={<ProductsPage />} />
          
          {/* Ruta 404 (Not Found) */}
          <Route path="*" element={
            <div className="text-center py-5">
              <h1 className="display-1 fw-bold text-muted">404</h1>
              <h2>Página no encontrada</h2>
              <p>Lo sentimos, la página que buscas no existe o fue movida.</p>
            </div>
          } />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
