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
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            padding-top: 80px;
            color: #2d3748;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Header amélioré */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.98);
            padding: 15px 20px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            backdrop-filter: blur(5px);
        }

        header a {
            color: #4a5568;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        header a:hover {
            background-color: #ebf4ff;
            color: #2b6cb0;
            transform: translateY(-2px);
        }

        /* Conteneur principal */
        .container {
            background-color: white;
            padding: 50px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
            position: relative;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Icône de confirmation */
        .confirmation-icon {
            font-size: 5rem;
            color: #48bb78;
            margin-bottom: 20px;
            animation: bounce 1s ease infinite alternate;
        }

        @keyframes bounce {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(-15px);
            }
        }

        /* Titres */
        h1 {
            color: #2d3748;
            font-size: 2.2rem;
            margin-bottom: 20px;
            position: relative;
        }

        h1::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #667eea, #5a67d8);
            margin: 20px auto;
            border-radius: 2px;
        }

        /* Texte */
        p {
            color: #718096;
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.7;
        }

        /* Boutons - SECTION AMÉLIORÉE */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            min-width: 200px;
        }

        .btn i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .btn-logout {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #c53030, #9b2c2c);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-products {
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
        }

        .btn-products:hover {
            background: linear-gradient(135deg, #5a67d8, #4c51bf);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                padding: 12px;
                gap: 10px;
            }

            header a {
                padding: 6px 12px;
                font-size: 0.9rem;
            }

            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 1.8rem;
            }

            .confirmation-icon {
                font-size: 4rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>

</head>

<body>
    <div class="container">
        <h1>🎉 Votre commande a été validée avec succès !</h1>
        <p>Merci pour votre achat. Nous vous enverrons une confirmation par e-mail.</p>


        <div class="action-buttons">

            <a href="product_list.php" class="btn btn-products">
                🛍️ Retour aux produits
            </a>
            <a href="../controllers/logout.php" class="btn btn-logout">
                🔒 Déconnexion
            </a>
        </div>
    </div>
</body>

</html>