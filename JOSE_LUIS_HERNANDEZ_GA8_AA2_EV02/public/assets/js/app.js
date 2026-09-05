/**
 * Lógica en cliente para la aplicación KenkoPOS
 */

// Confirmación de eliminación de producto
function confirmDelete(productId) {
    if (confirm('¿Está seguro de que desea eliminar este producto? Esta acción no se puede deshacer.')) {
        // Crear un formulario dinámicamente para hacer un POST y eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete.php';

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'product_id';
        inputId.value = productId;

        form.appendChild(inputId);
        document.body.appendChild(form);
        form.submit();
    }
}
