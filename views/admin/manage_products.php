<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../home.php');
    exit;
}

require_once '../db/connection.php';


$produits = $conn->query("SELECT * FROM products");

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Gestion des Produits</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .actions {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <h2>Liste des Produits</h2>

    <a href="dashboard.php">Retour au Dashboard</a>

    <table>
        <tr>
            <th>Nom</th>
            <th>Prix</th>
            <th>Actions</th>
        </tr>
        <?php while ($p = $produits->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= number_format($p['price'], 2) ?> Dh</td>
                <td class="actions">
                    <a href="../controllers/ProductController.php?edit=<?= $p['id'] ?>">Modifier</a>
                    <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>