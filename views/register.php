<?php
//register
session_start();
require_once '../db/connection.php';

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Vérification des champs
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Tous les champs sont requis.";
        header("Location: ../views/register.php");
        exit;
    }

    // Vérifier si l'utilisateur existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Cet e-mail est déjà utilisé.";
        $stmt->close();
        header("Location: ../views/register.php");
        exit;
    }
    $stmt->close();

    // Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insérer le nouvel utilisateur
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Inscription réussie. Connectez-vous maintenant.";
        header("Location: ../views/login.php");
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de l'inscription.";
        header("Location: ../views/register.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inscription</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* Styles existants */
        /* Reset de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Corps de page */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Conteneur du formulaire */
        .login-container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Titre */
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        /* Champs du formulaire */
        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        /* Bouton */
        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #5c6bc0;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-container button:hover {
            background-color: #3f51b5;
        }

        /* Lien vers la connexion */
        .login-container p {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-container a {
            color: #5c6bc0;
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Inscription</h2>
        <form method="POST" action="../controllers/AuthController.php">
            <input type="text" name="name" placeholder="Nom" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" name="register">S'inscrire</button>
        </form>
        <p>Déjà inscrit ? <a href="login.php">Connectez-vous</a></p>
    </div>
</body>

</html>