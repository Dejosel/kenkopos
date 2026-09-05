import { useState, useEffect } from 'react';

/**
 * ProductForm
 * 
 * Formulario para crear o editar un producto.
 * Implementa validaciones locales antes de enviar al padre.
 * 
 * @param {object} initialData Datos iniciales si es edición
 * @param {function} onSubmit Función que recibe los datos válidos
 * @param {boolean} isSubmitting Estado de envío
 */
const ProductForm = ({ initialData, onSubmit, isSubmitting }) => {
  const [formData, setFormData] = useState({
    name: '',
    sku: '',
    price: '',
    category: '',
    color: ''
  });

  const [errors, setErrors] = useState({});

  // Cargar datos iniciales si existen
  useEffect(() => {
    if (initialData) {
      setFormData({
        name: initialData.name || '',
        sku: initialData.sku || '',
        price: initialData.price || '',
        category: initialData.category || '',
        color: initialData.color || ''
      });
    } else {
      // Limpiar al crear nuevo
      setFormData({
        name: '',
        sku: '',
        price: '',
        category: '',
        color: ''
      });
    }
    setErrors({});
  }, [initialData]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
    // Limpiar error del campo que se está editando
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: null }));
    }
  };

  const validate = () => {
    const newErrors = {};
    if (!formData.name) newErrors.name = 'El nombre es obligatorio';
    else if (formData.name.length > 100) newErrors.name = 'El nombre no puede exceder 100 caracteres';

    if (!formData.sku) newErrors.sku = 'El SKU es obligatorio';

    if (!formData.price) newErrors.price = 'El precio es obligatorio';
    else if (Number(formData.price) <= 0) newErrors.price = 'El precio debe ser mayor a 0';

    if (!formData.category) newErrors.category = 'La categoría es obligatoria';

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (validate()) {
      onSubmit(formData);
    }
  };

  return (
    <form onSubmit={handleSubmit} noValidate>
      <div className="mb-3">
        <label className="form-label">Nombre del Producto *</label>
        <input 
          type="text" 
          name="name" 
          className={`form-control ${errors.name ? 'is-invalid' : ''}`} 
          value={formData.name} 
          onChange={handleChange} 
          maxLength="100"
        />
        {errors.name && <div className="invalid-feedback">{errors.name}</div>}
      </div>

      <div className="mb-3">
        <label className="form-label">SKU *</label>
        <input 
          type="text" 
          name="sku" 
          className={`form-control ${errors.sku ? 'is-invalid' : ''}`} 
          value={formData.sku} 
          onChange={handleChange} 
        />
        {errors.sku && <div className="invalid-feedback">{errors.sku}</div>}
      </div>

      <div className="row mb-3">
        <div className="col-md-6">
          <label className="form-label">Precio *</label>
          <input 
            type="number" 
            name="price" 
            step="0.01" 
            className={`form-control ${errors.price ? 'is-invalid' : ''}`} 
            value={formData.price} 
            onChange={handleChange} 
          />
          {errors.price && <div className="invalid-feedback">{errors.price}</div>}
        </div>
        <div className="col-md-6 mt-3 mt-md-0">
          <label className="form-label">Categoría *</label>
          <input 
            type="text" 
            name="category" 
            className={`form-control ${errors.category ? 'is-invalid' : ''}`} 
            value={formData.category} 
            onChange={handleChange} 
          />
          {errors.category && <div className="invalid-feedback">{errors.category}</div>}
        </div>
      </div>

      <div className="mb-4">
        <label className="form-label">Color (Opcional)</label>
        <input 
          type="text" 
          name="color" 
          className="form-control" 
          value={formData.color} 
          onChange={handleChange} 
        />
      </div>

      <div className="d-flex justify-content-end gap-2">
        <button 
          type="submit" 
          className="btn btn-primary" 
          disabled={isSubmitting}
        >
          {isSubmitting ? 'Guardando...' : 'Guardar Producto'}
        </button>
      </div>
    </form>
  );
};

export default ProductForm;
