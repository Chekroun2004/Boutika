<?php
//login.php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Conteneur principal */
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 450px;
            text-align: center;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Titre */
        .login-container h2 {
            color: #2d3748;
            margin-bottom: 25px;
            font-size: 1.8rem;
            position: relative;
        }

        .login-container h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, #667eea, #5a67d8);
            margin: 15px auto 0;
            border-radius: 2px;
        }

        /* Messages d'erreur/succès */
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        /* Champs de formulaire */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
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

        /* Bouton */
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn:hover {
            background: linear-gradient(135deg, #5a67d8, #4c51bf);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Liens */
        .login-links {
            margin-top: 25px;
            font-size: 0.9rem;
            color: #4a5568;
        }

        .login-links a {
            color: #5c6bc0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .login-links a:hover {
            color: #3f51b5;
            text-decoration: underline;
        }

        .home-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #5c6bc0;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .home-link:hover {
            color: #3f51b5;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .login-container h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <a href="../views/home.php" class="home-link">← Retour à l'accueil</a>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <h2>Connexion</h2>

        <form method="POST" action="../controllers/AuthController.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Votre mot de passe" required>
            </div>

            <button type="submit" name="login" class="btn">Se connecter</button>
        </form>

        <div class="login-links">
            <p>Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
        </div>
    </div>
</body>

</html>