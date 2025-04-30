<?php
//cart.php
session_start();
require_once '../db/connection.php';
require_once '../models/Product.php';
require_once '../models/Cart.php';

$userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
$cartItems = [];
$totalCartPrice = 0;

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['product_id']);

    if (isset($_POST['remove_from_cart'])) {
        if ($userId) {
            Cart::removeFromCart($userId, $productId);
        } else {
            unset($_SESSION['cart'][$productId]);
        }
    } elseif (isset($_POST['update_quantity'])) {
        $newQuantity = intval($_POST['quantity']);
        if ($newQuantity > 0) {
            if ($userId) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("iii", $newQuantity, $userId, $productId);
                $stmt->execute();
            } else {
                $_SESSION['cart'][$productId] = $newQuantity;
            }
        }
    }

    header("Location: cart.php");
    exit;
}

// Récupération du panier
if ($userId) {
    $cartItems = Cart::getCartByUserId($userId);
} else if (!empty($_SESSION['cart'])) {
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
        }
    }
}

// Calcul du total
foreach ($cartItems as $item) {
    $totalCartPrice += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Votre Panier</title>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #e4e8f0);
            min-height: 100vh;
            padding-top: 80px;
            color: #2d3748;
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.98);
            padding: 15px 20px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            backdrop-filter: blur(5px);
        }

        header a {
            color: #4a5568;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        header a:hover {
            background-color: #ebf4ff;
            color: #2b6cb0;
            transform: translateY(-2px);
        }

        /* Conteneur principal */
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        /* Titre */
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2d3748;
            font-size: 2.2rem;
            position: relative;
        }

        h1::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #667eea, #5a67d8);
            margin: 15px auto 0;
            border-radius: 2px;
        }

        h2 {
            text-align: right;
            margin-top: 20px;
            color: #2d3748;
            font-size: 1.5rem;
        }

        /* Tableau */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
            text-align: center;
            padding: 15px;
            font-weight: 600;
        }

        td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #edf2f7;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        /* Contrôle de quantité */
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .quantity-input {
            width: 60px;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-align: center;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .quantity-input:focus {
            outline: none;
            border-color: #5c6bc0;
            box-shadow: 0 0 0 2px rgba(92, 107, 192, 0.2);
        }

        /* Boutons */
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }

        .update-btn {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .update-btn:hover {
            background: linear-gradient(135deg, #38a169, #2f855a);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .remove-btn {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            padding: 8px 12px;
        }

        .remove-btn:hover {
            background: linear-gradient(135deg, #c53030, #9b2c2c);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .action-cell {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        /* Message panier vide */
        .empty-cart {
            text-align: center;
            padding: 40px;
            color: #718096;
            font-size: 1.1em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                padding: 10px;
                gap: 8px;
            }

            header a {
                padding: 6px 10px;
                font-size: 0.9rem;
            }

            .container {
                padding: 20px;
            }

            table {
                font-size: 0.8em;
            }

            td,
            th {
                padding: 8px 10px;
            }

            .quantity-control {
                flex-direction: column;
                gap: 5px;
            }

            .quantity-input {
                width: 50px;
            }
        }
    </style>
</head>

<body>
    <header>
        <a href="product_list.php">🛒 Voir les produits</a>
        <a href="home.php">🏠 Page Principale</a>
        <a href="checkout.php">Finaliser la commande</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="order_history.php">📜 Historique des achats</a>
            <a href="../controllers/logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Se connecter</a>
        <?php endif; ?>
    </header>
    <div class="container">
        <h1>Votre Panier</h1>
        <?php if (empty($cartItems)): ?>
            <p class="empty-cart">Votre panier est vide.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['price'], 2) ?> €</td>
                        <td>
                            <form method="POST" action="" class="quantity-control">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>"
                                    min="1" class="quantity-input">
                                <button type="submit" name="update_quantity" class="btn update-btn">✓</button>
                            </form>
                        </td>
                        <td><?= number_format($item['total_price'], 2) ?> €</td>
                        <td class="action-cell">
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <button type="submit" name="remove_from_cart" class="btn remove-btn">🗑️</button>
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