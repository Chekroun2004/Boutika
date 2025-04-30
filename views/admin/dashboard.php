<?php
// dashboard.php
require_once '../../models/Product.php';

$products = Product::getAllProducts();
$editProduct = null;
if (isset($_GET['edit'])) {
    $editProduct = Product::getProductById($_GET['edit']);
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../../views/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
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
            padding: 80px 20px 40px;
        }

        /* Conteneur principal */
        .dashboard-container {
            background: white;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        /* Titres */
        h1,
        h2 {
            text-align: center;
            color: #2d3748;
            margin-bottom: 30px;
            position: relative;
        }

        h1 {
            font-size: 2rem;
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

        /* Actions dashboard */
        .dashboard-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8, #4c51bf);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Formulaire */
        .product-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #f8fafc;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #5c6bc0;
            box-shadow: 0 0 0 3px rgba(92, 107, 192, 0.2);
            background-color: white;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .submit-btn {
            grid-column: span 2;
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            padding: 14px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #38a169, #2f855a);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Tableau des produits */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .products-table th {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
            padding: 15px;
            text-align: left;
        }

        .products-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #edf2f7;
        }

        .products-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .products-table tr:hover {
            background-color: #ebf4ff;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Actions produits */
        .product-actions {
            white-space: nowrap;
        }

        .action-link {
            color: #5c6bc0;
            text-decoration: none;
            margin-right: 10px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .action-link:hover {
            color: #3f51b5;
            text-decoration: underline;
        }

        .action-link.delete {
            color: #e53e3e;
        }

        .action-link.delete:hover {
            color: #c53030;
        }

        /* Déconnexion */
        .logout-container {
            text-align: center;
            margin-top: 40px;
        }

        .logout-link {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .logout-link:hover {
            background: linear-gradient(135deg, #c53030, #9b2c2c);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .product-form {
                grid-template-columns: 1fr;
            }

            .form-group.full-width,
            .submit-btn {
                grid-column: span 1;
            }

            .products-table {
                display: block;
                overflow-x: auto;
            }

            .dashboard-actions {
                justify-content: center;
            }
        }

        img.product-thumbnail {
            width: 60px;
            height: auto;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <h1>🛠️ Tableau de bord - Produits</h1>

        <div class="dashboard-actions">
            <a href="/boutique_en_ligne/views/admin/manage_users.php" class="btn btn-primary">👥 Gérer les clients</a>
        </div>

        <h2><?= $editProduct ? "✏️ Modifier un produit" : "➕ Ajouter un nouveau produit" ?></h2>

        <form class="product-form" action="/boutique_en_ligne/controllers/ProductController.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?= $editProduct ? 'edit' : 'add' ?>">
            <?php if ($editProduct): ?>
                <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="category">Catégorie :</label>
                <input type="text" id="category" name="category" required value="<?= htmlspecialchars($editProduct['category'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="price">Prix (€) :</label>
                <input type="number" step="0.01" id="price" name="price" required value="<?= $editProduct['price'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label for="stock">Stock :</label>
                <input type="number" id="stock" name="stock" required value="<?= $editProduct['stock'] ?? '' ?>">
            </div>

            <div class="form-group full-width">
                <label for="description">Description :</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">Image :</label>
                <input type="file" id="image" name="image">
                <?php if ($editProduct && $editProduct['image']): ?>
                    <div style="margin-top: 10px;">
                        <img src="../<?= htmlspecialchars($editProduct['image']) ?>" class="product-thumbnail">

                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="submit-btn">
                <?= $editProduct ? "💾 Mettre à jour" : "✅ Ajouter" ?>
            </button>
        </form>

        <h2>📦 Produits existants</h2>

        <table class="products-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Catégorie</th>
                    <th>Stock</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</td>
                        <td><?= number_format($product['price'], 2) ?> €</td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><?= $product['stock'] ?></td>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="../<?= htmlspecialchars($product['image']) ?>" class="product-thumbnail">

                            <?php endif; ?>
                        </td>
                        <td class="product-actions">
                            <a href="?edit=<?= $product['id'] ?>" class="action-link">Modifier</a>
                            <a href="/boutique_en_ligne/controllers/ProductController.php?delete=<?= $product['id'] ?>"
                                class="action-link delete"
                                onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="logout-container">
            <a href="dashboard.php?logout=true" class="logout-link">Déconnexion</a>
        </div>
    </div>
</body>

</html>