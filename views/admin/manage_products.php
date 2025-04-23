<?php
//manage_products
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../home.php');
    exit;
}
require_once '../db/connection.php';

// Ajout produit
if (isset($_POST['add'])) {
    // Code pour insérer un produit
}

// Modification produit
if (isset($_POST['edit'])) {
    // Code pour modifier un produit
}

// Suppression produit
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->query("DELETE FROM products WHERE id = $id");
    header("Location: manage_product.php");
    exit;
}
?>

<!-- Formulaire d'ajout/modification -->
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nom" placeholder="Nom du produit" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="number" name="prix" placeholder="Prix" required>
    <input type="text" name="categorie" placeholder="Catégorie" required>
    <input type="file" name="image">
    <button type="submit" name="add">Ajouter</button>
</form>

<!-- Liste des produits -->
<?php
$produits = $db->query("SELECT * FROM products");
while ($p = $produits->fetch()) {
    echo "<div>
        <p>{$p['nom']} - {$p['prix']} DH</p>
        <a href='manage_product.php?edit={$p['id']}'>Modifier</a>
        <a href='manage_product.php?delete={$p['id']}' onclick='return confirm(\"Supprimer ce produit ?\")'>Supprimer</a>
    </div>";
}
?>