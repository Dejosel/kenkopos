<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../app/Controllers/ProductController.php';
use App\Controllers\ProductController;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $controller = new ProductController();
    $controller->destroy((int)$_POST['product_id']);
} else {
    header("Location: list.php");
    exit();
}
