<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../app/Controllers/ProductController.php';
use App\Controllers\ProductController;

$controller = new ProductController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller->update($_POST);
}

// Obtener datos actuales del producto
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: list.php");
    exit();
}

$product = $controller->show((int)$id);
if (!$product) {
    header("Location: list.php?error=not_found");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - KenkoPOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- Navbar con logo -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 mb-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="../pos.php">
            <img src="../assets/img/logo_kenkopos.png" alt="KenkoPOS" height="38" style="object-fit:contain;">
        </a>
        <div class="d-flex gap-2 ms-auto">
            <a href="list.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver al Catálogo
            </a>
            <a href="../pos.php" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-receipt me-1"></i> POS
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square fs-5"></i>
                        <h4 class="mb-0">Editar Producto</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_GET['error'])): ?>
                            <div class="alert alert-danger">Error al actualizar. Verifica los datos.</div>
                        <?php endif; ?>

                        <form action="edit.php?id=<?= $product['product_id'] ?>" method="POST">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                            
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" class="form-control" id="sku" name="sku" value="<?= htmlspecialchars($product['sku']) ?>" required maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required maxlength="255">
                            </div>
                            <?php $currentCat = $product['category'] ?? 'General'; ?>
                            <div class="mb-3">
                                <label for="category" class="form-label">Categoría</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="Platos Fuertes" <?= $currentCat === 'Platos Fuertes' ? 'selected' : '' ?>>Platos Fuertes</option>
                                    <option value="Entradas" <?= $currentCat === 'Entradas' ? 'selected' : '' ?>>Entradas</option>
                                    <option value="Bebidas" <?= $currentCat === 'Bebidas' ? 'selected' : '' ?>>Bebidas</option>
                                    <option value="Postres" <?= $currentCat === 'Postres' ? 'selected' : '' ?>>Postres</option>
                                    <option value="Farmacia" <?= $currentCat === 'Farmacia' ? 'selected' : '' ?>>Farmacia</option>
                                    <option value="General" <?= $currentCat === 'General' ? 'selected' : '' ?>>General</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Precio ($)</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="color" class="form-label">Color de Botón en POS</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color" id="color" name="color" value="<?= htmlspecialchars($product['color'] ?? '#0d6efd') ?>" title="Seleccionar color">
                                    <span class="text-muted">Haz clic en el cuadro para elegir un color distintivo.</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="list.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
