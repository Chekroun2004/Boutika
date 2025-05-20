<?php
//ProductController
require_once '../db/connection.php';
require_once '../models/Product.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $category = $_POST['category'];
    $stock = intval($_POST['stock']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../views/' . $image);
    }

    if ($action === 'add') {
        Product::addProduct($name, $description, $price, $category, $image, $stock);
    }

    if ($action === 'edit') {
        Product::updateProduct($id, $name, $description, $price, $category, $image, $stock);
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    Product::deleteProduct($id);
}

header("Location: ../views/admin/dashboard.php");
exit;
