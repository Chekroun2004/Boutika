<?php
//order_history
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

// ✅ Récupération des commandes de l'utilisateur avec `user_order_number` unique
$stmt = $conn->prepare("
    SELECT id, user_order_number, total_price, payment_method, status, created_at 
    FROM orders 
    WHERE user_id = ? 
    GROUP BY id, user_order_number, total_price, payment_method, status, created_at
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Historique des Achats</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            min-height: 100vh;
            padding-top: 100px;
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

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .order {
            background: #fdfdfd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .order h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #444;
        }

        .order p {
            margin: 5px 0;
            color: #555;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .order-table th,
        .order-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .order-table th {
            background: #5c6bc0;
            color: white;
        }

        .order-table tr:nth-child(even) {
            background: #f4f4f4;
        }

        .btn-primary {
            display: inline-block;
            margin: 10px 10px 0 0;
            padding: 10px 20px;
            background: #5c6bc0;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #3f51b5;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📜 Historique de vos achats</h1>

        <?php if (empty($orders)): ?>
            <p>Aucune commande passée pour le moment.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order">
                    <h2>Commande #<?= htmlspecialchars($order['user_order_number']) ?> - <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></h2>
                    <p><strong>Total :</strong> <?= number_format($order['total_price'], 2) ?> €</p>
                    <p><strong>Méthode de paiement :</strong> <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></p>
                    <p><strong>Statut :</strong> <?= ucfirst($order['status']) ?></p>

                    <!-- Récupérer les articles de la commande -->
                    <?php
                    $stmt_items = $conn->prepare("
                        SELECT oi.*, p.name FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?
                    ");
                    $stmt_items->bind_param("i", $order['id']);
                    $stmt_items->execute();
                    $items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
                    ?>

                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Prix Unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= number_format($item['price'], 2) ?> €</td>
                                    <td><?= number_format($item['quantity'] * $item['price'], 2) ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="product_list.php" class="btn-primary">Retour aux produits</a>
        <a href="home.php" class="btn-primary">Retour à la page d'accueil</a>
    </div>
</body>

</html>