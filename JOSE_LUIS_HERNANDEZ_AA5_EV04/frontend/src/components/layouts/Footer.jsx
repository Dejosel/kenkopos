/**
 * Footer
 * 
 * Pie de página de la aplicación.
 */
const Footer = () => {
  return (
    <footer className="bg-white border-top text-center text-muted py-3 mt-auto">
      <div className="container-fluid">
        <small>© {new Date().getFullYear()} KenkoPOS. Evidencia SENA GA7-220501096-AA4-EV03.</small>
      </div>
    </footer>
  );
};

export default Footer;
