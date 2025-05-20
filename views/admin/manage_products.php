<?php
// manage_product.php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../home.php');
    exit;
}
require_once '../db/connection.php';

// Récupération des produits
$produits = $conn->query("SELECT * FROM products");
$editProduct = null;

// Mode édition (sans traitement)
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $editProduct = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
}

// Suppression produit (gardé car simple)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: manage_product.php");
    exit;
}
?>

<!-- Formulaire d'ajout/modification -->
<form method="POST" action="../controllers/ProductController.php" enctype="multipart/form-data">
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
            <img src="../<?= htmlspecialchars($editProduct['image']) ?>" class="product-thumbnail">
        <?php endif; ?>
    </div>

    <button type="submit" class="submit-btn">
        <?= $editProduct ? "💾 Mettre à jour" : "✅ Ajouter" ?>
    </button>
</form>

<!-- Liste des produits -->
<table>
    <?php while ($p = $produits->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= number_format($p['price'], 2) ?> €</td>
            <td>
                <a href="?edit=<?= $p['id'] ?>">Modifier</a>
                <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>