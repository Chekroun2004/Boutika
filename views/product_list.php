<?php
//product_list
session_start();
require_once '../db/connection.php';
require_once '../models/Product.php';

// Vérifier si l'utilisateur est connecté
$userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
$message = "";

// Ajouter un produit au panier (base de données ou session)
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) $quantity = 1;

    // Si l'utilisateur est connecté : base de données
    if ($userId) {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity)
                                 VALUES (?, ?, ?)
                                 ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->bind_param("iiii", $userId, $product_id, $quantity, $quantity);
        $stmt->execute();
    } else {
        // Utilisateur non connecté : stocker en session
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    $message = "Produit ajouté au panier !";
}

// Récupération des produits avec filtres
$sort_order = $_GET['sort_order'] ?? 'asc';
$category = $_GET['category'] ?? '';
$in_stock = isset($_GET['in_stock']);

$query = "SELECT * FROM products WHERE 1";
$params = [];
$types = "";

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}
if ($in_stock) $query .= " AND stock > 0";

$query .= " ORDER BY price " . ($sort_order === 'desc' ? 'DESC' : 'ASC');
$stmt = $conn->prepare($query);

if (!empty($params)) $stmt->bind_param($types, ...$params);

$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Produits</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Styles existants */
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
        <a href="cart.php">🛒 Voir le Panier</a> |
        <a href="home.php">🏠 Page Principale</a> |
        <a href="checkout.php">Commander</a> |
        <?php if ($userId): ?>
            <a href="order_history.php">📜 Historique des achats</a>
            <a href="../controllers/logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Se connecter</a>
        <?php endif; ?>
    </header>

    <h1>Liste des Produits</h1>
    <?php if ($message): ?>
        <p class="alert"><?= $message ?></p>
    <?php endif; ?>

    <form method="GET">
        <label>Catégorie:
            <select name="category">
                <option value="">Toutes</option>
                <option value="Electronique">Électronique</option>
                <option value="Vêtements">Vêtements</option>
                <option value="Électroménager">Électroménager</option>
            </select>
        </label>
        <label>Stock uniquement:
            <input type="checkbox" name="in_stock" <?= $in_stock ? 'checked' : '' ?>>
        </label>
        <label>Trier par prix:
            <select name="sort_order">
                <option value="asc" <?= $sort_order === 'asc' ? 'selected' : '' ?>>Croissant</option>
                <option value="desc" <?= $sort_order === 'desc' ? 'selected' : '' ?>>Décroissant</option>
            </select>
        </label>
        <button type="submit">Filtrer</button>
    </form>

    <?php foreach ($products as $product): ?>
        <div class="product">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <?php if (!empty($product['image'])): ?>
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"style="max-width: 200px; max-height: 200px; display: block; margin: 10px 0;">

            <?php endif; ?>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <strong><?= number_format($product['price'], 2) ?> €</strong>
            <form method="POST" action="">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="number" name="quantity" value="1" min="1">
                <button type="submit" name="add_to_cart">Ajouter au panier</button>
            </form>
            <hr>
        </div>
    <?php endforeach; ?>

</body>

</html>