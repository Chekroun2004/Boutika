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

        /* Contenu principal */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Titre */
        h1 {
            text-align: center;
            margin: 30px 0;
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
            margin: 10px auto 0;
            border-radius: 2px;
        }

        /* Message d'alerte */
        .alert {
            background: #48bb78;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 20px auto;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Formulaire de filtrage */
        form[method="GET"] {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin: 30px auto;
            max-width: 800px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            justify-content: center;
        }

        form[method="GET"] label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            color: #4a5568;
        }

        form[method="GET"] select,
        form[method="GET"] input[type="number"],
        form[method="GET"] input[type="checkbox"] {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        form[method="GET"] select:focus,
        form[method="GET"] input:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }

        form[method="GET"] button {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        form[method="GET"] button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Grille de produits */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }

        /* Carte de produit - MODIFICATIONS PRINCIPALES ICI */
        .product {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Centre horizontalement tous les éléments enfants */
            text-align: center;
            /* Centre le texte */
        }

        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
        }

        .product h3 {
            color: #2d3748;
            font-size: 1.25rem;
            margin-bottom: 10px;
            width: 100%;
            /* Prend toute la largeur pour un centrage correct */
        }

        .product img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            margin: 10px 0;
            border-radius: 8px;
            background: #f8fafc;
            padding: 10px;
        }

        .product p {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 15px;
            width: 100%;
            /* Prend toute la largeur pour un centrage correct */
        }

        .product strong {
            display: block;
            font-size: 1.3rem;
            color: #2b6cb0;
            margin: 15px 0;
            font-weight: 700;
            width: 100%;
            /* Prend toute la largeur pour un centrage correct */
        }

        .product form {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            /* Prend toute la largeur */
            justify-content: center;
            /* Centre le formulaire */
        }

        .product input[type="number"] {
            width: 70px;
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-align: center;
        }

        .product button {
            background: linear-gradient(135deg, #5c6bc0, #5c6bc0);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .product button:hover {
            background: linear-gradient(135deg, rgb(80, 97, 188), rgb(80, 97, 188));
            transform: translateY(-2px);
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

            form[method="GET"] {
                flex-direction: column;
                align-items: stretch;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

</head>

<body>
    <header>
        <a href="cart.php"> Voir le Panier</a> 
        <a href="home.php"> Page Principale</a> 
        <a href="checkout.php">Commander</a> 
        <?php if ($userId): ?>
            <a href="order_history.php"> Historique des achats</a>
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
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 200px; max-height: 200px; display: block; margin: 10px 0;">

            <?php endif; ?>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <strong><?= number_format($product['price'], 2) ?> Dh</strong>
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