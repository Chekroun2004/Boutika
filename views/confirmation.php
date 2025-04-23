<?php
//confirmation
session_start(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Commande Confirmée</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Styles globaux */
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
            text-align: center;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }

        h2 {
            font-size: 20px;
            color: #5c6bc0;
            margin-top: 30px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-top: 15px;
        }

        ul li {
            background: #f8f9fa;
            padding: 10px;
            margin: 5px 0;
            border-radius: 6px;
            color: #333;
        }

        p strong {
            font-size: 1.2em;
            color: #333;
        }

        form {
            margin-top: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 12px;
            color: #444;
        }

        input[type="radio"] {
            margin-right: 8px;
            transform: scale(1.2);
        }

        button {
            background: #28a745;
            color: white;
            padding: 10px 25px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #218838;
        }

        .btn-secondary {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }

        .btn-secondary:hover {
            background: #545b62;
        }
    </style>

</head>

<body>
    <div class="container">

        <a href="../controllers/logout.php" class="btn-secondary">Déconnexion</a>
        <h1>🎉 Votre commande a été validée avec succès !</h1>
        <p>Merci pour votre achat. Nous vous enverrons une confirmation par e-mail.</p>

        <a href="product_list.php" class="btn-secondary">Retour aux produits</a>
    </div>
</body>

</html>