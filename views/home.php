<?php 
//home
session_start(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Boutique en ligne</title>
    <style>
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
        <a href="product_list.php">Produits</a>
        <a href="cart.php">Panier</a>
        <a href="checkout.php">Commander</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="order_history.php">📜 Historique</a>
            <a href="../controllers/logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Se connecter</a>
        <?php endif; ?>
    </header>

    <div class="home-container">
        <h1>Bienvenue sur la boutique en ligne</h1>
        <p>Découvrez nos produits et profitez des meilleures offres !</p>
    </div>

</body>

</html>