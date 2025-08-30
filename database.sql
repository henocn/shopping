-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : sam. 30 août 2025 à 10:46
-- Version du serveur : 8.0.43-0ubuntu0.22.04.1
-- Version de PHP : 8.1.2-1ubuntu2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `shopping`
--

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `pack_id` int NOT NULL,
  `quantity` int NOT NULL,
  `total_price` int NOT NULL,
  `client_name` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `client_country` int NOT NULL,
  `client_adress` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `client_note` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `manager_id` int NOT NULL,
  `manager_note` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('processing','validated','rejected','canceled') COLLATE utf8mb4_general_ci NOT NULL,
  `action` enum('remind','call','unreachable') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `price` int NOT NULL,
  `quantity` int NOT NULL,
  `image` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `carousel1` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `carousel2` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `carousel3` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `carousel4` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `carousel5` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text NULL,
  `status` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Structure de la table `product_caracteristics`
--

CREATE TABLE `product_caracteristics` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_mentions`
--

CREATE TABLE `product_video` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `texte` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_packs`
--

CREATE TABLE `product_packs` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price_reduction` int DEFAULT NULL,
  `price_normal` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `role` int NOT NULL DEFAULT '0',
  `country` int NOT NULL DEFAULT '228'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `country`) VALUES
(1, 'tchamie@gmail.com', '$2y$12$tOzGoyI3IHho9Xqiyi908OjXxFXCl9v9/Kpb.hI0dGSC07OMpq2Bu', 1, 228);
--
-- Index pour les tables déchargées
--

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `product_caracteristics`
--
ALTER TABLE `product_caracteristics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_mentions`
--
ALTER TABLE `product_video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_packs`
--
ALTER TABLE `product_packs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);


--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product_functionalities`
--


--
-- AUTO_INCREMENT pour la table `product_video`
--
ALTER TABLE `product_video`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product_packs`
--
ALTER TABLE `product_packs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `product_caracteristics`
--
ALTER TABLE `product_caracteristics`
  ADD CONSTRAINT `product_caracteristics_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `product_mentions`
--
ALTER TABLE `product_video`
  ADD CONSTRAINT `product_mentions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `product_packs`
--
ALTER TABLE `product_packs`
  ADD CONSTRAINT `product_packs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
