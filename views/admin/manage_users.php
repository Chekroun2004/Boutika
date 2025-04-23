<?php
// views/admin/manage_users.php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../home.php');
    exit;
}

require_once '../../db/connection.php'; // ✅ chemin corrigé aussi !
$clients = $conn->query("SELECT * FROM users WHERE role = 'user'");

// Suppression d’un utilisateur
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'client'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

// Modification d’un utilisateur
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

// Déconnexion
if (isset($_GET['logout'])) {
    session_unset(); // Détruire toutes les variables de session
    session_destroy(); // Détruire la session elle-même
    header('Location: ../home.php'); // Rediriger vers la page d'accueil
    exit;
}

// Récupération des clients
$clients = $conn->query("SELECT * FROM users WHERE email != 'admin@site.com'");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Gestion des clients</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }

        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .dashboard-link,
        .logout-link {
            display: inline-block;
            margin-bottom: 20px;
            font-size: 16px;
            color: #5c6bc0;
            text-decoration: none;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 8px;
        }

        .dashboard-link:hover,
        .logout-link:hover {
            background-color: #ddd;
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

        input[type="text"],
        input[type="email"] {
            padding: 5px;
            width: 150px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        a {
            color: #e74c3c;
            text-decoration: none;
            padding: 5px;
            font-size: 14px;
        }

        a:hover {
            color: #c0392b;
        }

        .actions {
            white-space: nowrap;
        }

        form {
            display: inline;
        }
    </style>
</head>

<body>
    <a href="dashboard.php" class="dashboard-link">📊 Accéder au Tableau de Bord</a>
    <a href="manage_users.php?logout=true" class="logout-link">🚪 Déconnexion</a>

    <h2>👥 Liste des clients inscrits</h2>
    <table>
        <?php while ($client = $clients->fetch_assoc()): ?>
            <form method="POST" action="manage_users.php">
                <tr>
                    <td>
                        <input type="text" name="name" value="<?= htmlspecialchars($client['name']) ?>">
                    </td>
                    <td>
                        <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>">
                    </td>
                    <td class="actions">
                        <input type="hidden" name="id" value="<?= $client['id'] ?>">
                        <button type="submit" name="update">Modifier</button>
                        <a href="manage_users.php?delete=<?= $client['id'] ?>" onclick="return confirm('Supprimer ce client ?')">🗑 Supprimer</a>
                    </td>
                </tr>
            </form>
        <?php endwhile; ?>
    </table>
</body>

</html>