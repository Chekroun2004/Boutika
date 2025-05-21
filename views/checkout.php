<?php
// checkout.php - Version harmonisée
session_start();
require_once '../db/connection.php';
require_once '../models/Cart.php';
require_once '../models/Product.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

if (isset($_GET['remove_item'])) {
    Cart::removeFromCart($userId, (int)$_GET['remove_item']);
    header('Location: checkout.php');
    exit;
}

$cartItems = Cart::getCartByUserId($userId);
$totalCartPrice = 0;

if (empty($cartItems)) {
?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Panier vide</title>
        <style>
            /* Animation */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #f5f7fa, #e4e8f0);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .empty-message {
                background: white;
                padding: 40px;
                border-radius: 16px;
                box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
                max-width: 600px;
                text-align: center;
                animation: fadeIn 0.6s ease-out;
            }

            .empty-message h1 {
                color: #e53e3e;
                margin-bottom: 20px;
                font-size: 1.8rem;
            }

            .empty-message p {
                font-size: 1.1rem;
                color: #718096;
                margin-bottom: 25px;
            }

            .action-buttons {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin-top: 30px;
                flex-wrap: wrap;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 12px 25px;
                border-radius: 8px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .btn-home {
                background: linear-gradient(135deg, #667eea, #5a67d8);
                color: white;
            }

            .btn-home:hover {
                background: linear-gradient(135deg, #5a67d8, #4c51bf);
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .btn-products {
                background: #e2e8f0;
                color: #4a5568;
            }

            .btn-products:hover {
                background: #cbd5e0;
            }
        </style>
    </head>

    <body>
        <div class="empty-message">
            <h1>🛒 Votre panier est vide</h1>
            <p>Vous devez ajouter des produits à votre panier avant de pouvoir passer commande.</p>
            <div class="action-buttons">
                <a href="home.php" class="btn btn-home">🏠 Accueil</a>
                <a href="product_list.php" class="btn btn-products">🛍️ Voir les produits</a>
            </div>
        </div>
    </body>

    </html>
<?php
    exit;
}

foreach ($cartItems as $item) {
    $product = Product::getProductById($item['id']);
    if (!$product || $product['stock'] < $item['quantity']) {
        $_SESSION['error'] = 'Le produit "' . htmlspecialchars($item['name']) . '" n\'est plus disponible en quantité suffisante';
        header('Location: cart.php');
        exit;
    }
    $totalCartPrice += $item['price'] * $item['quantity'];
}

if (isset($_POST['place_order'])) {
    $stmt = $conn->prepare("SELECT IFNULL(MAX(user_order_number), 0) + 1 FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($userOrderNumber);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO orders (user_id, user_order_number, total_price, payment_method, status) VALUES (?, ?, ?, ?, 'en attente')");
    $stmt->bind_param("iids", $userId, $userOrderNumber, $totalCartPrice, $_POST['payment_method']);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    foreach ($cartItems as $item) {
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, {$item['id']}, {$item['quantity']}, {$item['price']})");
        $conn->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['id']}");
    }

    $conn->query("DELETE FROM cart WHERE user_id = $userId");
    header("Location: confirmation.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Finaliser la commande</title>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #e4e8f0);
            min-height: 100vh;
            padding-top: 80px;
            color: #2d3748;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 30px auto;
        }

        h1 {
            text-align: center;
            color: #2d3748;
            margin-bottom: 30px;
            font-size: 2rem;
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
            text-align: center;
            color: #4a5568;
            margin: 25px 0 15px;
            font-size: 1.4rem;
        }

        /* Liste des produits */
        .cart-items {
            list-style: none;
            padding: 0;
            margin: 25px 0;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 12px;
            background: #f8fafc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            background: #edf2f7;
            transform: translateX(5px);
        }

        .remove-item {
            color: #e53e3e;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            margin-left: 15px;
        }

        /* Total */
        .total-price {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #2b6cb0;
            margin: 30px 0;
        }

        /* Options de paiement */
        .payment-options {
            margin: 30px 0;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 12px;
            background: #f8fafc;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            background: #ebf4ff;
        }

        .payment-option input {
            margin-right: 15px;
        }

        /* Boutons */
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #38a169, #2f855a);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .btn-container {
                flex-direction: column;
                gap: 12px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Finaliser la commande</h1>

        <h2>Résumé de votre commande</h2>
        <ul class="cart-items">
            <?php foreach ($cartItems as $item): ?>
                <li class="cart-item">
                    <span>
                        <?= htmlspecialchars($item['name']) ?> -
                        <?= $item['quantity'] ?> x <?= number_format($item['price'], 2) ?> Dh
                    </span>
                    <a href="checkout.php?remove_item=<?= $item['id'] ?>"
                        class="remove-item"
                        title="Supprimer">🗑️</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="total-price">
            Total : <?= number_format($totalCartPrice, 2) ?> Dh
        </div>

        <form method="POST">
            <h2>Mode de paiement</h2>

            <div class="payment-options">
                <label class="payment-option">
                    <input type="radio" name="payment_method"
                        value="paiement_en_ligne" required>
                    <span>💳 Paiement en ligne</span>
                </label>

                <label class="payment-option">
                    <input type="radio" name="payment_method"
                        value="paiement_a_la_livraison">
                    <span>🚚 Paiement à la livraison</span>
                </label>
            </div>

            <div class="btn-container">
                <button type="submit" name="place_order" class="btn btn-primary">
                    ✅ Valider la commande
                </button>
                <a href="product_list.php" class="btn btn-secondary">
                    ↩️ Retour aux produits
                </a>
            </div>
        </form>
    </div>
</body>

</html>