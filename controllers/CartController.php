<?php
//CartController
session_start();
require_once '../db/connection.php';

if (isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user']['id'];
    $product_id = ($_POST['product_id']);
    $quantity = ($_POST['quantity']);

    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)
              ON DUPLICATE KEY UPDATE quantity = quantity + ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
    $stmt->execute();
}
?>
