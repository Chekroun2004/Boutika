<?php
// product_list.php
session_start();
require_once '../db/connection.php';
require_once '../models/Product.php';

// Vérifier si l'utilisateur est connecté
$userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
$message = "";

// Ajouter un produit au panier
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) $quantity = 1;

    if ($userId) {
        // Utilisateur connecté : panier en base de données
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity)
                               VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->bind_param("iiii", $userId, $product_id, $quantity, $quantity);
        $stmt->execute();
    } else {
        // Utilisateur non connecté : panier en session
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $_SESSION['cart'][$product_id] = isset($_SESSION['cart'][$product_id])
            ? $_SESSION['cart'][$product_id] + $quantity
            : $quantity;
    }

    $message = "Produit ajouté au panier !";
}

// Récupération des paramètres de recherche/filtrage
$search_query = trim($_GET['search'] ?? '');
$sort_order = $_GET['sort_order'] ?? 'asc';
$category = $_GET['category'] ?? '';
$in_stock = isset($_GET['in_stock']);

// Construction de la requête SQL
$query = "SELECT * FROM products WHERE 1";
$params = [];
$types = "";

// Filtre de recherche (optionnel)
if (!empty($search_query)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $search_term = "%$search_query%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

// Filtres supplémentaires
if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if ($in_stock) {
    $query .= " AND stock > 0";
}

// Tri
$query .= " ORDER BY price " . ($sort_order === 'desc' ? 'DESC' : 'ASC');

// Exécution de la requête
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Produits</title>
    <style>
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

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

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

        /* Formulaire de recherche principal */
        .search-main {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin: 30px auto;
            max-width: 800px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search-form input[type="text"] {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 1rem;
            min-width: 300px;
        }

        .search-form button {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .search-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Filtres supplémentaires - Nouveau style aligné */
        .filters-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .filters-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 10px 0;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .filter-group label {
            font-weight: 500;
            color: #4a5568;
            white-space: nowrap;
        }

        .filter-group select {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            cursor: pointer;
        }

        .filter-group input[type="checkbox"] {
            cursor: pointer;
        }

        .reset-btn {
            background: #e53e3e;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .reset-btn:hover {
            background: #c53030;
            transform: translateY(-2px);
        }

        /* Grille de produits */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }

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
            text-align: center;
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
        }

        .product strong {
            display: block;
            font-size: 1.3rem;
            color: #2b6cb0;
            margin: 15px 0;
            font-weight: 700;
            width: 100%;
        }

        .product form {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            justify-content: center;
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

        /* Message aucun résultat */
        .no-results {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .no-results p {
            font-size: 1.2rem;
            color: #4a5568;
            margin-bottom: 20px;
        }

        .no-results a {
            color: #5a67d8;
            text-decoration: none;
            font-weight: 600;
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

            .search-form {
                flex-direction: column;
            }

            .search-form input[type="text"] {
                min-width: auto;
                width: 100%;
            }

            .filters-row {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .filter-group {
                justify-content: space-between;
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
        <a href="checkout.php"> Commander</a>
        <?php if ($userId): ?>
            <a href="order_history.php"> Historique des achats</a>
            <a href="../controllers/logout.php"> Déconnexion</a>
        <?php else: ?>
            <a href="login.php"> Se connecter</a>
        <?php endif; ?>
    </header>

    <div class="container">
        <h1>Liste des Produits</h1>

        <?php if ($message): ?>
            <p class="alert"><?= $message ?></p>
        <?php endif; ?>

        <div class="search-main">
            <form method="GET" class="search-form">
                <input type="text" name="search"
                    placeholder="Rechercher un produit par nom ou description..."
                    value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit">🔍 Rechercher</button>
            </form>

            <div class="filters-container">
                <form method="GET">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search_query) ?>">

                    <div class="filters-row">
                        <div class="filter-group">
                            <label>Catégorie:</label>
                            <select name="category" onchange="this.form.submit()">
                                <option value="">Toutes</option>
                                <option value="Electronique" <?= $category === 'Electronique' ? 'selected' : '' ?>>Électronique</option>
                                <option value="Vêtements" <?= $category === 'Vêtements' ? 'selected' : '' ?>>Vêtements</option>
                                <option value="Électroménager" <?= $category === 'Électroménager' ? 'selected' : '' ?>>Électroménager</option>
                                <option value="Livres" <?= $category === 'Livres' ? 'selected' : '' ?>>Livres</option>
                                <option value="Jouets" <?= $category === 'Jouets' ? 'selected' : '' ?>>Jouets</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <input type="checkbox" id="in_stock" name="in_stock" onchange="this.form.submit()" <?= $in_stock ? 'checked' : '' ?>>
                            <label for="in_stock">En stock seulement</label>
                        </div>

                        <div class="filter-group">
                            <label>Trier par prix:</label>
                            <select name="sort_order" onchange="this.form.submit()">
                                <option value="asc" <?= $sort_order === 'asc' ? 'selected' : '' ?>>Croissant</option>
                                <option value="desc" <?= $sort_order === 'desc' ? 'selected' : '' ?>>Décroissant</option>
                            </select>
                        </div>

                        <button type="button" onclick="location.href='product_list.php'" class="reset-btn">
                            Réinitialiser
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="no-results">
                <p>Aucun produit trouvé avec ces critères de filtrage.</p>
                <a href="product_list.php">Réinitialiser les filtres</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php endif; ?>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                        <p style="color: <?= $product['stock'] > 0 ? '#38a169' : '#e53e3e' ?>;">
                            <?= $product['stock'] > 0 ? 'En stock: ' . $product['stock'] : 'Rupture de stock' ?>
                        </p>
                        <strong><?= number_format($product['price'], 2) ?> Dh</strong>
                        <form method="POST" action="">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="number" name="quantity" min="1"
                                value="1" max="<?= $product['stock'] ?>"
                                <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                            <button type="submit" name="add_to_cart"
                                <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                                <?= $product['stock'] > 0 ? 'Ajouter au panier' : 'Indisponible' ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Soumission automatique des filtres
        document.querySelectorAll('.filter-group select, .filter-group input[type="checkbox"]').forEach(element => {
            element.addEventListener('change', function() {
                // Garder la valeur de recherche si elle existe
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput && searchInput.value) {
                    // Ne rien faire, le formulaire sera soumis normalement
                } else {
                    // Soumettre le formulaire
                    this.closest('form').submit();
                }
            });
        });
    </script>
</body>

</html>