<?php
//header
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Header - Boutique</title>
    <style>
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
</body>

</html>