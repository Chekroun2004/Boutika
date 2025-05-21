<?php
//order_history.php - Version harmonisée
session_start();
require_once '../db/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

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
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #e4e8f0);
            min-height: 100vh;
            padding-top: 80px;
            color: #2d3748;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 30px auto;
        }

        h1 {
            text-align: center;
            color: #2d3748;
            margin-bottom: 30px;
            font-size: 2rem;
            position: relative;
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

        /* Styles des commandes */
        .order {
            background: #f8fafc;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-title {
            font-size: 1.2rem;
            color: #2d3748;
            font-weight: 600;
        }

        .order-meta {
            color: #718096;
            font-size: 0.9rem;
        }

        .order-details {
            margin-top: 15px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }

        .detail-label {
            font-weight: 600;
            color: #4a5568;
            min-width: 150px;
        }

        .detail-value {
            color: #718096;
        }

        /* Tableau des produits */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .products-table th {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
            padding: 12px;
            text-align: left;
        }

        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #edf2f7;
        }

        .products-table tr:nth-child(even) {
            background: #f8fafc;
        }

        /* Boutons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8, #4c51bf);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Message vide */
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #718096;
            font-size: 1.1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .products-table {
                display: block;
                overflow-x: auto;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-buttons {
                flex-direction: column;
                gap: 12px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📜 Historique des achats</h1>

        <?php if (empty($orders)): ?>
            <p class="empty-message">Aucune commande passée pour le moment.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order">
                    <div class="order-header">
                        <h2 class="order-title">Commande #<?= htmlspecialchars($order['user_order_number']) ?></h2>
                        <span class="order-meta"><?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?></span>
                    </div>

                    <div class="order-details">
                        <div class="detail-row">
                            <span class="detail-label">Total :</span>
                            <span class="detail-value"><?= number_format($order['total_price'], 2) ?> Dh</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Méthode de paiement :</span>
                            <span class="detail-value"><?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Statut :</span>
                            <span class="detail-value"><?= ucfirst($order['status']) ?></span>
                        </div>
                    </div>

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

                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= number_format($item['price'], 2) ?> Dh</td>
                                    <td><?= number_format($item['quantity'] * $item['price'], 2) ?> Dh</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="home.php" class="btn btn-primary">🏠 Accueil</a>
            <a href="product_list.php" class="btn btn-primary">🛍️ Boutique</a>