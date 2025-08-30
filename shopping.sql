-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 29 août 2025 à 11:06
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

CREATE TABLE `orders` IF NOT EXISTS (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `pack_id` int NOT NULL,
  `quantity` int NOT NULL,
  `client_name` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `client_country` int NOT NULL,
  `client_adress` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `client_note` int NOT NULL,
  `manager_id` int NOT NULL,
  `manager_note` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('processing','validated','rejected','canceled','') COLLATE utf8mb4_general_ci NOT NULL,
  `action` enum('remind','call','unreachable','') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` IF NOT EXISTS (
  `id` int NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `price` int NOT NULL,
  `quantity` int NOT NULL,
  `image` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_additional_descriptions`
--

CREATE TABLE `product_additional_descriptions` IF NOT EXISTS (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `texte` text,
  `is_active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_caracteristics`
--

CREATE TABLE `product_caracteristics` IF NOT EXISTS  (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `titre` varchar(100) NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_carousel`
--

CREATE TABLE `product_carousel` IF NOT EXISTS (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `is_active` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_descriptive_images`
--

CREATE TABLE `product_descriptive_images` IF NOT EXISTS (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image_url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_functionalities`
--

CREATE TABLE `product_functionalities` IF NOT EXISTS (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `elements_1` varchar(255) DEFAULT NULL,
  `elements_2` varchar(255) DEFAULT NULL,
  `elements_3` varchar(255) DEFAULT NULL,
  `elements_4` varchar(255) DEFAULT NULL,
  `functionality_order` int NOT NULL,
  `is_active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_mentions`
--

CREATE TABLE `product_mentions` IF NOT EXISTS (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `texte` text,
  `is_active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_packs`
--

CREATE TABLE `product_packs` IF NOT EXISTS (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `pack_order` int NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantite` int DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `price_reduction` int DEFAULT NULL,
  `price_normal` int DEFAULT NULL,
  `is_active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_recommandation`
--

CREATE TABLE `product_recommandation` IF NOT EXISTS (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `conseil_texte` text,
  `is_active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` IF NOT EXISTS (
  `id` int NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `role` int NOT NULL DEFAULT '0',
  `pays` int NOT NULL DEFAULT '228'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `pays`) VALUES
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `product_additional_descriptions`
--
ALTER TABLE `product_additional_descriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_caracteristics`
--
ALTER TABLE `product_caracteristics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_carousel`
--
ALTER TABLE `product_carousel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_descriptive_images`
--
ALTER TABLE `product_descriptive_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_functionalities`
--
ALTER TABLE `product_functionalities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_mentions`
--
ALTER TABLE `product_mentions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_packs`
--
ALTER TABLE `product_packs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_recommandation`
--
ALTER TABLE `product_recommandation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

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
-- AUTO_INCREMENT pour la table `product_additional_descriptions`
--
ALTER TABLE `product_additional_descriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product_carousel`
--
ALTER TABLE `product_carousel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product_descriptive_images`
--
ALTER TABLE `product_descriptive_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product_functionalities`
--
ALTER TABLE `product_functionalities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product_mentions`
--
ALTER TABLE `product_mentions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product_packs`
--
ALTER TABLE `product_packs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product_recommandation`
--
ALTER TABLE `product_recommandation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `product_additional_descriptions`
--
ALTER TABLE `product_additional_descriptions`
  ADD CONSTRAINT `product_additional_descriptions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `product_caracteristics`
--
ALTER TABLE `product_caracteristics`
  ADD CONSTRAINT `product_caracteristics_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `product_carousel`
--
ALTER TABLE `product_carousel`
  ADD CONSTRAINT `product_carousel_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `product_descriptive_images`
--
ALTER TABLE `product_descriptive_images`
  ADD CONSTRAINT `product_descriptive_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `product_functionalities`
--
ALTER TABLE `product_functionalities`
  ADD CONSTRAINT `product_functionalities_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `product_mentions`
--
ALTER TABLE `product_mentions`
  ADD CONSTRAINT `product_mentions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `product_packs`
--
ALTER TABLE `product_packs`
  ADD CONSTRAINT `product_packs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `product_recommandation`
--
ALTER TABLE `product_recommandation`
  ADD CONSTRAINT `product_recommandation_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
