<?php
// views/admin/manage_users.php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../home.php');
    exit;
}

require_once '../../db/connection.php';

// Récupération de tous les utilisateurs sauf admin
$stmt = $conn->prepare("SELECT * FROM users WHERE email != 'admin@site.com'");
$stmt->execute();
$clients = $stmt->get_result();

// Suppression d'un utilisateur
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND email != 'admin@site.com'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

// Modification d'un utilisateur
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ? AND email != 'admin@site.com'");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute();
        header("Location: manage_users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Clients</title>
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
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .btn-danger {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c53030, #9b2c2c);
        }

        /* Tableau */
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .users-table th {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
            padding: 15px;
            text-align: left;
        }

        .users-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #edf2f7;
        }

        .users-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .users-table tr:hover {
            background-color: #ebf4ff;
        }

        /* Champs de formulaire */
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #5c6bc0;
            box-shadow: 0 0 0 3px rgba(92, 107, 192, 0.2);
            background-color: white;
        }

        /* Boutons */
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #38a169, #2f855a);
        }

        /* Actions */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-actions {
                flex-direction: column;
                gap: 15px;
            }

            .users-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <h1>👥 Gestion des Clients</h1>

        <div class="dashboard-actions">
            <a href="dashboard.php" class="btn btn-primary">📊 Retour au Dashboard</a>
            <a href="manage_users.php?logout=true" class="btn btn-danger">🚪 Déconnexion</a>
        </div>

        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($clients->num_rows > 0): ?>
                    <?php while ($client = $clients->fetch_assoc()): ?>
                        <tr>
                            <form method="POST" action="manage_users.php">
                                <td><?= htmlspecialchars($client['id']) ?></td>
                                <td>
                                    <input type="text" name="name" class="form-control"
                                        value="<?= htmlspecialchars($client['name']) ?>">
                                </td>
                                <td>
                                    <input type="email" name="email" class="form-control"
                                        value="<?= htmlspecialchars($client['email']) ?>">
                                </td>
                                <td><?= htmlspecialchars($client['role'] ?? 'user') ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                        <button type="submit" name="update" class="btn btn-success btn-sm">
                                            💾 Enregistrer
                                        </button>
                                        <a href="manage_users.php?delete=<?= $client['id'] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Supprimer ce client ?')">
                                            🗑 Supprimer
                                        </a>
                                    </div>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Aucun utilisateur trouvé</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>