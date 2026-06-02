<?php
require_once __DIR__ . '/../../app/Controllers/ProductController.php';
use App\Controllers\ProductController;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller = new ProductController();
    $controller->store($_POST);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto - KenkoPOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Crear Nuevo Producto</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_GET['error'])): ?>
                            <div class="alert alert-danger">Por favor, verifica los datos e inténtalo de nuevo.</div>
                        <?php endif; ?>

                        <form action="create.php" method="POST">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU (Código único)</label>
                                <input type="text" class="form-control" id="sku" name="sku" required maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" id="name" name="name" required maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Precio</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="list.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-success">Guardar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
