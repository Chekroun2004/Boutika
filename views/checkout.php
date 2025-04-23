<?php
//checkout
session_start();
require_once '../db/connection.php';
require_once '../models/Cart.php';
require_once '../models/Product.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$cartItems = Cart::getCartByUserId($userId);
$totalCartPrice = 0;

// 🚨 Vérification : interdire la finalisation si le panier est vide
if (empty($cartItems)) {
?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Panier vide</title>
        <link rel="stylesheet" href="../assets/css/style.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #74ebd5, #9face6);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                padding: 20px;
            }

            .empty-message {
                background: white;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                max-width: 600px;
            }

            h1 {
                color: #e74c3c;
                margin-bottom: 20px;
            }

            p {
                font-size: 18px;
                color: #555;
            }

            a {
                display: inline-block;
                margin: 15px 10px 0;
                padding: 10px 20px;
                background: #5c6bc0;
                color: white;
                border-radius: 6px;
                text-decoration: none;
                transition: background 0.3s ease;
            }

            a:hover {
                background: #3f51b5;
            }
        </style>
    </head>

    <body>
        <div class="empty-message">
            <h1>🚫 Panier vide</h1>
            <p>Il est impossible d'effectuer une commande avec un panier vide.</p>
            <p>Veuillez ajouter des articles à votre panier avant de finaliser une commande.</p>
            <a href="home.php">🏠 Retour à l'accueil</a>
            <a href="product_list.php">🛍️ Voir les produits</a>
        </div>
    </body>

    </html>
<?php
    exit;
}


// 🚨 Recalculer le total et vérifier les stocks en temps réel
foreach ($cartItems as $item) {
    $product = Product::getProductById($item['id']);
    if (!$product || $product['stock'] < $item['quantity']) {
        echo "<script>alert('Le produit " . htmlspecialchars($item['name']) . " est en rupture de stock ou quantité insuffisante.'); window.location.href = 'cart.php';</script>";
        exit;
    }
    $totalCartPrice += $item['price'] * $item['quantity'];
}

// 🚨 Valider la commande
if (isset($_POST['place_order'])) {
    $payment_method = $_POST['payment_method'];

    // 🔹 Récupérer le dernier numéro de commande de l'utilisateur
    $stmt = $conn->prepare("SELECT IFNULL(MAX(user_order_number), 0) + 1 FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($userOrderNumber);
    $stmt->fetch();
    $stmt->close();

    // 🔹 Insérer la commande avec le bon numéro de commande
    $stmt = $conn->prepare("INSERT INTO orders (user_id, user_order_number, total_price, payment_method, status) VALUES (?, ?, ?, ?, 'en attente')");
    $stmt->bind_param("iids", $userId, $userOrderNumber, $totalCartPrice, $payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // 🔹 Insérer chaque produit dans la commande et décrémenter le stock
    foreach ($cartItems as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->bind_param("ii", $item['quantity'], $item['id']);
        $stmt->execute();
    }

    // 🔹 Vider le panier de la base de données
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // 🔹 Redirection vers la page de confirmation
    header("Location: confirmation.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Finaliser la commande</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            min-height: 100vh;
            padding-top: 100px;
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

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
            margin: auto;
            text-align: center;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }

        h2 {
            font-size: 20px;
            color: #5c6bc0;
            margin-top: 30px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-top: 15px;
        }

        ul li {
            background: #f8f9fa;
            padding: 10px;
            margin: 5px 0;
            border-radius: 6px;
            color: #333;
        }

        p strong {
            font-size: 1.2em;
            color: #333;
        }

        form {
            margin-top: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 12px;
            color: #444;
        }

        input[type="radio"] {
            margin-right: 8px;
            transform: scale(1.2);
        }

        button {
            background: #28a745;
            color: white;
            padding: 10px 25px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #218838;
        }

        .btn-secondary {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }

        .btn-secondary:hover {
            background: #545b62;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Finaliser la commande</h1>
        <h2>Résumé de votre commande</h2>
        <ul>
            <?php foreach ($cartItems as $item): ?>
                <li><?= htmlspecialchars($item['name']) ?> - <?= $item['quantity'] ?> x <?= number_format($item['price'], 2) ?> €</li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total :</strong> <?= number_format($totalCartPrice, 2) ?> €</p>

        <h2>Choisissez votre mode de paiement :</h2>
        <form method="POST">
            <label>
                <input type="radio" name="payment_method" value="paiement_en_ligne" required>
                💳 Paiement en ligne
            </label>
            <label>
                <input type="radio" name="payment_method" value="paiement_a_la_livraison">
                🚚 Paiement à la livraison
            </label>

            <button type="submit" name="place_order">✅ Valider la commande</button>
        </form>

        <a href="product_list.php" class="btn-secondary">↩️ Retour aux produits</a>


    </div>
</body>

</html>