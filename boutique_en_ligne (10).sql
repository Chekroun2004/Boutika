-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 19 mai 2025 à 14:56
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `boutique_en_ligne`
--

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(156, 2, 3, 1, '2025-04-23 15:45:17'),
(157, 2, 26, 1, '2025-04-23 15:45:19'),
(158, 2, 12, 1, '2025-04-23 15:45:20'),
(160, 2, 12, 2, '2025-05-01 10:59:55'),
(164, 12, 11, 4, '2025-05-18 18:54:25'),
(165, 12, 13, 2, '2025-05-18 18:54:25');

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('en attente','expédiée','livrée') DEFAULT 'en attente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(100) NOT NULL,
  `user_order_number` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`, `payment_method`, `user_order_number`) VALUES
(19, 3, 62.97, 'en attente', '2025-03-25 16:36:33', 'paiement_en_ligne', 1),
(20, 3, 1239.97, 'en attente', '2025-03-25 16:36:47', 'paiement_a_la_livraison', 2),
(21, 3, 119.97, 'en attente', '2025-03-25 16:41:11', 'paiement_en_ligne', 3),
(22, 3, 28.98, 'en attente', '2025-04-05 13:50:24', 'paiement_en_ligne', 4),
(24, 3, 39.97, 'en attente', '2025-04-07 13:48:38', 'paiement_en_ligne', 5),
(25, 3, 9.99, 'en attente', '2025-04-07 13:49:36', 'paiement_en_ligne', 6),
(26, 3, 999.99, 'en attente', '2025-04-09 11:50:25', 'paiement_en_ligne', 7),
(27, 9, 187.97, 'en attente', '2025-04-10 19:37:40', 'paiement_a_la_livraison', 1),
(28, 9, 1139.97, 'en attente', '2025-04-10 19:37:58', 'paiement_a_la_livraison', 2),
(30, 6, 1548.99, 'en attente', '2025-04-12 12:04:32', 'paiement_en_ligne', 1),
(31, 10, 2156.97, 'en attente', '2025-04-14 11:25:29', 'paiement_en_ligne', 1),
(32, 2, 2148.00, 'en attente', '2025-04-17 10:17:58', 'paiement_en_ligne', 1),
(33, 2, 8.99, 'en attente', '2025-04-17 10:18:13', 'paiement_en_ligne', 2),
(34, 9, 64.89, 'en attente', '2025-04-17 10:26:17', 'paiement_en_ligne', 3),
(35, 9, 28.98, 'en attente', '2025-04-17 10:33:48', 'paiement_a_la_livraison', 4),
(36, 9, 235.99, 'en attente', '2025-04-17 10:35:32', 'paiement_a_la_livraison', 5),
(37, 9, 8.99, 'en attente', '2025-04-30 16:42:48', 'paiement_en_ligne', 6),
(38, 3, 19.99, 'en attente', '2025-05-01 21:53:18', 'paiement_en_ligne', 8),
(39, 3, 48.99, 'en attente', '2025-05-03 14:38:32', 'paiement_en_ligne', 9),
(40, 9, 211.88, 'en attente', '2025-05-18 18:56:10', 'paiement_en_ligne', 7),
(41, 9, 3068.49, 'en attente', '2025-05-19 12:11:14', 'paiement_a_la_livraison', 8);

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(55, 30, 16, 1, 1199.00),
(56, 30, 21, 1, 349.99),
(57, 31, 6, 1, 1149.00),
(58, 31, 12, 1, 49.99),
(59, 31, 21, 1, 349.99),
(60, 31, 24, 1, 599.00),
(61, 31, 25, 1, 8.99),
(62, 32, 4, 1, 649.00),
(63, 32, 11, 1, 1499.00),
(64, 33, 25, 1, 8.99),
(65, 34, 3, 1, 12.90),
(66, 34, 8, 2, 21.50),
(67, 34, 25, 1, 8.99),
(68, 35, 20, 1, 19.99),
(69, 35, 25, 1, 8.99),
(70, 36, 8, 1, 21.50),
(71, 36, 9, 1, 149.00),
(72, 36, 18, 1, 11.50),
(73, 36, 25, 1, 8.99),
(74, 36, 26, 1, 45.00),
(75, 37, 25, 1, 8.99),
(76, 38, 20, 1, 19.99),
(77, 39, 13, 1, 9.99),
(78, 39, 23, 1, 39.00),
(79, 40, 3, 1, 12.90),
(80, 40, 13, 1, 9.99),
(81, 40, 25, 1, 8.99),
(82, 40, 26, 4, 45.00),
(83, 41, 8, 1, 21.50),
(84, 41, 11, 2, 1499.00),
(85, 41, 13, 1, 9.99),
(86, 41, 23, 1, 39.00);

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `image`, `stock`, `created_at`) VALUES
(1, 'iPhone 14 Pro', 'Smartphone Apple avec écran OLED 6.1\", puce A16 Bionic.', 1299.00, '0', 'iphone14pro.JPEG', 15, '2025-04-11 11:00:00'),
(2, 'T-shirt Nike Homme', 'T-shirt Nike en coton, respirant et confortable.', 29.99, 'Vêtements', 'tshirt_nike.WEBP', 50, '2025-04-11 11:00:00'),
(3, 'L’Alchimiste', 'Roman de Paulo Coelho sur la quête du bonheur.', 12.90, 'Livres', 'alchimiste.jpg', 58, '2025-04-11 11:00:00'),
(4, 'Aspirateur Dyson V15', 'Aspirateur sans fil puissant avec filtre HEPA.', 649.00, 'Électroménager', 'dyson_v15.WEBP', 5, '2025-04-11 11:00:00'),
(5, 'Lego Star Wars', 'Coffret Lego pour construire le Faucon Millenium.', 159.99, 'Jouets', 'lego_starwars.jpeg', 20, '2025-04-11 11:00:00'),
(6, 'Samsung Galaxy S23', 'Smartphone Android 6.1\", Snapdragon 8 Gen 2, 256 Go.', 1149.00, 'Électronique', 'galaxy_s23.WEBP', 11, '2025-04-11 11:00:00'),
(7, 'Jean Levis 501', 'Jean classique coupe droite pour homme.', 79.99, 'Vêtements', 'jean_levis.jpeg', 35, '2025-04-11 11:00:00'),
(8, 'Sapiens', 'Essai de Yuval Noah Harari sur l’histoire de l’humanité.', 21.50, 'Livres', 'sapiens.jpeg', 26, '2025-04-11 11:00:00'),
(9, 'Machine à café Nespresso', 'Machine à capsules avec mousseur à lait intégré.', 149.00, 'Électroménager', 'nespresso.png', 13, '2025-04-11 11:00:00'),
(10, 'Poupée Barbie Dreamhouse', 'Maison de rêve Barbie avec accessoires.', 129.00, 'Jouets', 'barbie_dreamhouse.WEBP', 22, '2025-04-11 11:00:00'),
(11, 'MacBook Air M2', 'Ordinateur portable Apple, puce M2, 8 Go RAM, 256 Go SSD.', 1499.00, 'Électronique', 'macbook_air_m2.jpeg', 5, '2025-04-11 11:00:00'),
(12, 'Robe Zara', 'Robe longue d’été pour femme, légère et élégante.', 49.99, 'Vêtements', 'robe_zara.jpg', 24, '2025-04-11 11:00:00'),
(13, 'Le Petit Prince', 'Conte philosophique d’Antoine de Saint-Exupéry.', 9.99, 'Livres', 'petit_prince.jpeg', 97, '2025-04-11 11:00:00'),
(14, 'Réfrigérateur Samsung', 'Frigo-congélateur avec technologie No Frost.', 899.00, 'Électroménager', 'frigo_samsung.jpeg', 4, '2025-04-11 11:00:00'),
(15, 'Voiture télécommandée', '4x4 tout-terrain télécommandé pour enfants.', 59.99, 'Jouets', 'voiture_rc.jpg', 30, '2025-04-11 11:00:00'),
(16, 'TV LG OLED 55\"', 'Télévision 4K OLED 55\", Dolby Vision, HDMI 2.1.', 1199.00, 'Électronique', 'tv_lg_oled.jpeg', 9, '2025-04-11 11:00:00'),
(17, 'Veste en cuir', 'Veste en cuir véritable pour homme.', 199.00, 'Vêtements', 'veste_cuir.jpeg', 12, '2025-04-11 11:00:00'),
(18, '1984', 'Roman dystopique de George Orwell.', 11.50, 'Livres', '1984.jpg', 44, '2025-04-11 11:00:00'),
(19, 'Four encastrable Bosch', 'Four électrique multifonctions avec pyrolyse.', 549.00, 'Électroménager', 'four_bosch.WEBP', 7, '2025-04-11 11:00:00'),
(20, 'Puzzle 1000 pièces', 'Puzzle paysage naturel, idéal pour les familles.', 19.99, 'Jouets', 'puzzle_1000.jpg', 40, '2025-04-11 11:00:00'),
(21, 'Écouteurs Sony WH-1000XM5', 'Casque Bluetooth à réduction de bruit active.', 349.99, 'Électronique', 'sony_wh1000xm5.WEBP', 18, '2025-04-11 11:00:00'),
(22, 'Baskets Adidas Superstar', 'Baskets blanches classiques à bandes noires.', 89.99, 'Vêtements', 'adidas_superstar.WEBP', 40, '2025-04-11 11:00:00'),
(23, 'Clean Code', 'Livre sur les bonnes pratiques de programmation.', 39.00, 'Livres', 'clean_code.jpg', 16, '2025-04-11 11:00:00'),
(24, 'Lave-linge LG 9kg', 'Machine à laver frontale avec moteur Inverter.', 599.00, 'Électroménager', 'lave_linge_lg.jpg', 4, '2025-04-11 11:00:00'),
(25, 'Jeu de société UNO', 'Jeu de cartes classique multijoueur.', 8.99, 'Jouets', 'uno.WEBP', 47, '2025-04-11 11:00:00'),
(26, 'Trottinette enfant', 'Trottinette pliable pour enfants 3-8 ans.', 45.00, 'Jouets', 'trotinette.jpg', 13, '2025-04-11 11:00:00'),
(28, 'test', 'test', 2222.00, 'test', '1747658333_GlassWave-Blue.jpg', 10, '2025-05-19 12:38:53');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','admin') DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'omar', 'omarchekroun39@gmail.com', '$2y$10$.0HcLNtKIjnsyCR5fICg2OcRamphuZPfYJV7/a5C0dc1kLmnfTcRi', 'client', '2025-03-15 14:41:58'),
(2, 'haytam', 'haytam@gmail.com', '$2y$10$E5v.5A4yEhfYLWYE88Ve0OzsPJ8qF26f/06EBw/X5JDsHtJK9.mZ.', 'client', '2025-03-15 15:53:27'),
(3, 'ali', 'ali@gmail.com', '$2y$10$pPvHOK7LvXZLAO2taXOONuEJ3xyDVgou4A.R5/rY8WgHAdYsaw7Lu', 'client', '2025-03-15 16:01:47'),
(6, 'bilal', 'bilal@gmail.com', '$2y$10$xTH9K1iJ4.tte8ZW1vYe4uBiRbvFdL9HNVm328dmxuK.h4FnSM6Xi', 'client', '2025-03-16 00:33:32'),
(8, 'Administrateur', 'admin@site.com', '$2y$10$m2Zz4qgrvuhTbv4FLXZ15ev6NmNYu5crYjExzghRIH2mKRLUmOROS\n', 'admin', '2025-04-09 18:36:26'),
(9, 'lina', 'lina@gmail.com', '$2y$10$WpjG86fgo5SlCv13pdMCDeYXTWZyrMLQayEjSBvfNLY0ANbPgUGM2', 'client', '2025-04-10 19:37:04'),
(10, 'lamiae', 'lamiae@gmail.com', '$2y$10$W8VENAuIqx1mrNPazFD9g.VtndXscrMEH2PwIY89n2DkEcYLAXVL6', 'client', '2025-04-14 11:23:39'),
(11, 'aaa', 'aaa@gmail.com', '$2y$10$r/obc1QdyKdQbLZZtkrQzuInzZ.Iy7YwQKvpjFNUf/xzQiqmEslN.', 'client', '2025-04-17 10:16:50'),
(12, 'Administrateur', 'admin@gmail.com', '$2y$10$IBPIYZfVv5JolFbKzTetW..w1bGnjGzCe8.MpzvoL6H5lpN7DrMtW', 'admin', '2025-05-16 16:38:05');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Index pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_products_category` (`category`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT pour la table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
