<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../app/Controllers/ProductController.php';
use App\Controllers\ProductController;

$controller = new ProductController();
$products = $controller->index();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos - KenkoPOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestión de Productos - KenkoPOS</h2>
            <div>
                <a href="../pos.php" class="btn btn-success me-2">Pantalla POS (SambaPOS)</a>
                <a href="create.php" class="btn btn-primary">Nuevo Producto</a>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Operación realizada con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Color de Botón</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['product_id']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($product['sku']) ?></span></td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($product['category'] ?? 'General') ?></span></td>
                                    <td>
                                        <?php if(!empty($product['color'])): ?>
                                            <div class="d-flex align-items-center">
                                                <span class="d-inline-block rounded-circle me-2" style="width: 15px; height: 15px; background-color: <?= htmlspecialchars($product['color']) ?>; border: 1px solid #777;"></span>
                                                <code><?= htmlspecialchars($product['color']) ?></code>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Por defecto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>$ <?= number_format($product['price'], 2) ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $product['product_id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $product['product_id'] ?>)">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay productos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>
</html>
