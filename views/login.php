<?php
//login
session_start();
if (isset($_SESSION['error'])) {
    echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
if (isset($_SESSION['message'])) {
    echo "<p style='color:green'>" . $_SESSION['message'] . "</p>";
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Connexion</title>
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
        <a href="../views/home.php">Aller a la page d'aceuille</a>
        <h2>Connexion</h2>
        <form method="POST" action="../controllers/AuthController.php">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" name="login">Se connecter</button>
        </form>
        <p>Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
    </div>
</body>

</html>