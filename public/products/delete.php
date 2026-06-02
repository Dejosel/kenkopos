<?php
require_once __DIR__ . '/../../app/Controllers/ProductController.php';
use App\Controllers\ProductController;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $controller = new ProductController();
    $controller->destroy((int)$_POST['product_id']);
} else {
    header("Location: list.php");
    exit();
}
