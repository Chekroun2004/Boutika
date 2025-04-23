<?php
// dashboard.php
require_once '../../models/Product.php';

$products = Product::getAllProducts();
$editProduct = null;
if (isset($_GET['edit'])) {
    $editProduct = Product::getProductById($_GET['edit']);
}

// Logique de déconnexion
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../../views/login.php'); // Redirection vers la page de connexion après la déconnexion
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            padding: 100px 20px 40px;
            min-height: 100vh;
            margin: 0;
        }

        .dashboard-container {
            background-color: white;
            max-width: 1200px;
            margin: auto;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .dashboard-actions {
            text-align: right;
            margin-bottom: 20px;
        }

        .dashboard-actions a {
            text-decoration: none;
            background-color: #5c6bc0;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .dashboard-actions a:hover {
            background-color: #3f51b5;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }

        form label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }

        form input,
        form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        form textarea {
            resize: vertical;
            height: 100px;
        }

        form button {
            grid-column: span 2;
            background-color: #5c6bc0;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        form button:hover {
            background-color: #3f51b5;
        }

        .product-image-preview {
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        table th,
        table td {
            padding: 12px 15px;
            text-align: left;
        }

        table th {
            background-color: #5c6bc0;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table img {
            width: 60px;
            border-radius: 8px;
        }

        a {
            color: #5c6bc0;
            text-decoration: none;
            margin-right: 8px;
        }

        a:hover {
            text-decoration: underline;
        }

        .product-actions {
            text-align: center;
        }

        .logout-link {
            text-decoration: none;
            color: white;
            background-color: #e74c3c;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            display: block;
            text-align: center;
        }

        .logout-link:hover {
            background-color: #c0392b;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <h1>🛠️ Tableau de bord - Produits</h1>

        <div class="dashboard-actions">
            <a href="/boutique_en_ligne/views/admin/manage_users.php">👥 Gérer les clients</a>
        </div>

        <h2><?= $editProduct ? "✏️ Modifier un produit" : "➕ Ajouter un nouveau produit" ?></h2>

        <form action="/boutique_en_ligne/controllers/ProductController.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?= $editProduct ? 'edit' : 'add' ?>">
            <?php if ($editProduct): ?>
                <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
            <?php endif; ?>

            <div>
                <label>Nom :</label>
                <input type="text" name="name" required value="<?= $editProduct['name'] ?? '' ?>">
            </div>

            <div>
                <label>Catégorie :</label>
                <input type="text" name="category" required value="<?= $editProduct['category'] ?? '' ?>">
            </div>

            <div>
                <label>Prix (€) :</label>
                <input type="number" step="0.01" name="price" required value="<?= $editProduct['price'] ?? '' ?>">
            </div>

            <div>
                <label>Stock :</label>
                <input type="number" name="stock" required value="<?= $editProduct['stock'] ?? '' ?>">
            </div>

            <div style="grid-column: span 2;">
                <label>Description :</label>
                <textarea name="description" required><?= $editProduct['description'] ?? '' ?></textarea>
            </div>

            <div>
                <label>Image :</label>
                <input type="file" name="image">
                <?php if ($editProduct && $editProduct['image']): ?>
                    <div class="product-image-preview">
                        <img src="../uploads/<?= $editProduct['image'] ?>" width="80">
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit"><?= $editProduct ? "💾 Mettre à jour" : "✅ Ajouter" ?></button>
        </form>

        <h2>📦 Produits existants</h2>

        <table>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Catégorie</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td><?= number_format($product['price'], 2) ?> €</td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td><?= $product['stock'] ?></td>
                    <td>
                        <?php if ($product['image']): ?>
                            <img src="../uploads/<?= $product['image'] ?>">
                        <?php endif; ?>
                    </td>
                    <td class="product-actions">
                        <a href="?edit=<?= $product['id'] ?>">Modifier</a>
                        <a href="/boutique_en_ligne/controllers/ProductController.php?delete=<?= $product['id'] ?>" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Lien de déconnexion -->
        <a href="dashboard.php?logout=true" class="logout-link">Déconnexion</a>
    </div>
</body>

</html>