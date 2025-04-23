<?php
//cart
session_start();
require_once '../db/connection.php';
require_once '../models/Product.php';
require_once '../models/Cart.php';

// Identifier l'utilisateur
$userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
$cartItems = [];
$totalCartPrice = 0;

// Synchroniser la session avec la base de données si l'utilisateur est connecté
if ($userId) {
    $cartItems = Cart::getCartByUserId($userId);

    // 🚨 Correction : Recalculer le totalCartPrice
    foreach ($cartItems as $item) {
        $totalCartPrice += $item['price'] * $item['quantity'];
    }
} else {
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = Product::getProductById($productId);
            if ($product) {
                $cartItems[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'total_price' => $product['price'] * $quantity
                ];
                $totalCartPrice += $product['price'] * $quantity;
            }
        }
    }
}

// Supprimer un produit
if (isset($_POST['remove_from_cart'])) {
    $productId = intval($_POST['product_id']);
    if ($userId) {
        Cart::removeFromCart($userId, $productId);
    } else {
        unset($_SESSION['cart'][$productId]);
    }
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background: #333;
            color: white;
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
        }

        header a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        header a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
        }

        button {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #c82333;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        header {
            background: #333;
            color: white;
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
        }

        header a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        header a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .product {
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 8px;
            margin: 10px 0;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .product h3 {
            color: #007bff;
            margin: 0 0 10px;
        }

        .product p {
            color: #555;
        }

        .product strong {
            display: block;
            margin: 10px 0;
            font-size: 1.2em;
        }

        form {
            margin-top: 10px;
        }

        input[type="number"] {
            width: 50px;
            padding: 5px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .alert {
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        header {
            background-color: #ffffff;
            padding: 15px 20px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        header a {
            margin: 0 10px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        header a:hover {
            color: #5c6bc0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            min-height: 100vh;
            padding-top: 80px;
            /* espace pour le header */
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            padding: 15px 20px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        header a {
            margin: 0 10px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        header a:hover {
            color: #5c6bc0;
        }

        .home-container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
            margin: auto;
            text-align: center;
        }

        .home-container h1 {
            margin-bottom: 10px;
            color: #333;
        }

        .home-container p {
            font-size: 15px;
            color: #555;
        }
    </style>
</head>

<body>
    <header>
        <a href="product_list.php">🛒 Voir les produits</a> |
        <a href="home.php">🏠 Page Principale</a> |
        <a href="checkout.php">Finaliser la commande</a> |
        <?php if (isset($_SESSION['user'])): ?>
            <a href="order_history.php">📜 Historique des achats</a> |
            <a href="../controllers/logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Se connecter</a>
        <?php endif; ?>
    </header>
    <div class="container">
        <h1>Votre Panier</h1>
        <?php if (empty($cartItems)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?= $item['name'] ?></td>
                        <td><?= number_format($item['price'], 2) ?> €</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['total_price'], 2) ?> €</td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="remove_from_cart">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <h2>Total : <?= number_format($totalCartPrice, 2) ?> €</h2>
        <?php endif; ?>
    </div>
</body>

</html>