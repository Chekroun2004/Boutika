<?php
//home.php

session_start();

// Traitement de l'ajout au panier
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) $quantity = 1;

    // Si l'utilisateur est connecté
    if (isset($_SESSION['user'])) {
        require_once '../db/connection.php';
        require_once '../models/Cart.php';

        // Vérifier si le produit est déjà dans le panier
        $existingItem = Cart::getCartItem($_SESSION['user']['id'], $product_id);

        if ($existingItem) {
            // Mettre à jour la quantité
            $newQuantity = $existingItem['quantity'] + $quantity;
            Cart::updateCartItem($_SESSION['user']['id'], $product_id, $newQuantity);
        } else {
            // Ajouter un nouvel item
            Cart::addToCart($_SESSION['user']['id'], $product_id, $quantity);
        }
    } else {
        // Utilisateur non connecté - utiliser la session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    // Rediriger pour éviter la resoumission du formulaire
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Boutique en ligne</title>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            padding-top: 80px;
            color: #2d3748;
            line-height: 1.6;
        }

        /* Header amélioré */
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
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        header a:hover {
            background-color: #ebf4ff;
            color: #2b6cb0;
            transform: translateY(-2px);
        }

        /* Conteneur d'accueil */
        .home-container {
            background-color: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .home-container h1 {
            margin-bottom: 15px;
            color: #2d3748;
            font-size: 2.2rem;
            position: relative;
        }

        .home-container h1::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #667eea, #5a67d8);
            margin: 15px auto 0;
            border-radius: 2px;
        }

        .home-container p {
            font-size: 1.1rem;
            color: #718096;
            margin-bottom: 10px;
        }

        /* Grille de produits */
        .product-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 25px;
            margin: 40px auto;
            width: 90%;
            max-width: 1200px;
            padding: 0 20px;
        }

        /* Carte de produit améliorée */
        .product-card {
            background-color: #fff;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #f8fafc;
            padding: 15px;
        }

        .product-card h3 {
            color: #2d3748;
            font-size: 1.2rem;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .product-card p {
            color: #718096;
            font-size: 0.95rem;
            margin-bottom: 15px;
            flex-grow: 1;
        }

        .product-card .price {
            font-weight: 700;
            color: #2b6cb0;
            font-size: 1.3rem;
            margin: 15px 0;
        }

        .product-card form {
            width: 100%;
        }

        .product-card form button {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .product-card form button:hover {
            background: linear-gradient(135deg, #5a67d8, #4c51bf);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                padding: 12px;
                gap: 10px;
            }

            header a {
                padding: 6px 12px;
                font-size: 0.9rem;
            }

            .home-container {
                padding: 30px 20px;
            }

            .product-cards {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
        }
    </style>
</head>

<body>
    <header>
        <a href="product_list.php">Produits</a>
        <a href="cart.php">Panier</a>
        <a href="checkout.php">Commander</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="order_history.php"> Historique</a>
            <a href="../controllers/logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Se connecter</a>
        <?php endif; ?>
    </header>

    <div class="home-container">
        <h1>Bienvenue sur la boutique en ligne</h1>
        <p>Découvrez nos produits et profitez des meilleures offres !</p>
    </div>

    <div class="product-cards">
        <?php
        // Connexion à la base de données
        $conn = mysqli_connect("localhost", "root", "", "boutique_en_ligne");

        if (!$conn) {
            die("Erreur de connexion à la base de données.");
        }

        // Sélection de 4 produits aléatoires
        $query = "SELECT * FROM products ORDER BY RAND() LIMIT 4";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='product-card'>";

            $image = isset($row['image']) ? htmlspecialchars($row['image']) : '';
            $name = isset($row['name']) ? htmlspecialchars($row['name']) : 'Produit';
            $desc = isset($row['description']) ? htmlspecialchars($row['description']) : '';
            $price = isset($row['price']) ? htmlspecialchars($row['price']) : 'Non défini';
            $id = isset($row['id']) ? (int)$row['id'] : 0;

            if (!empty($image)) {
                echo "<img src='$image' alt='$name'>";
            } else {
                echo "<img src='images/default.jpg' alt='Image par défaut'>";
            }

            echo "<h3>$name</h3>";
            echo "<p>$desc</p>";
            echo "<p class='price'>$price €</p>";

            echo "<form method='post' action=''>
                <input type='hidden' name='product_id' value='$id'>
                <input type='hidden' name='quantity' value='1'>
                <button type='submit' name='add_to_cart'>Ajouter au panier</button>
            </form>";

            echo "</div>";
        }

        mysqli_close($conn);
        ?>
    </div>
</body>

</html>