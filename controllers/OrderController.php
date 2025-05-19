<?php
//OrderController
session_start();
require_once '../db/connection.php';

if (isset($_POST['place_order'])) {
    $user_id = $_SESSION['user']['id'];
    $total_price = 0;


    $cart_items = $conn->query("SELECT c.product_id, c.quantity, p.price 
                                FROM cart c 
                                JOIN products p ON c.product_id = p.id 
                                WHERE c.user_id = $user_id");

    while ($item = $cart_items->fetch_assoc()) {
        $total_price += $item['quantity'] * $item['price'];
    }

    // dkhelnaha f orders
    $conn->query("INSERT INTO orders (user_id, total_price) VALUES ($user_id, $total_price)");
    $order_id = $conn->insert_id;

    // dkhelnaha f  order_items pour plus de detailles 
    $cart_items->data_seek(0);
    while ($item = $cart_items->fetch_assoc()) {
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) 
                      VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})");
    }

    
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");

    echo "Commande passée avec succès !";
}
?>
