-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 26 fév. 2022 à 23:20
-- Version du serveur :  5.7.31
-- Version de PHP : 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `fashionform-w`
--

-- --------------------------------------------------------

--
-- Structure de la table `cadre`
--

DROP TABLE IF EXISTS `cadre`;
CREATE TABLE IF NOT EXISTS `cadre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F42587B912469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cadre`
--

INSERT INTO `cadre` (`id`, `category_id`, `name`) VALUES
(1, 1, 'cadré '),
(2, 1, 'Demi cadré'),
(3, 1, 'Sans cadre ');

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activer` tinyint(1) NOT NULL DEFAULT '1',
  `picture` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`, `slug`, `activer`, `picture`) VALUES
(1, 'Optique', 'optique', 1, 'product9-621242f24409c.jpg'),
(2, 'Horlogerie', 'horlogerie', 1, 'category4-621242e729756.jpg'),
(3, 'Parfumerie', 'parfumerie', 1, 'category2-620434bd11a35.jpg'),
(4, 'Occasion', 'occasion', 1, 'product22-620434dac8d09.jpg'),
(6, 'Accessoires', 'accessoires', 1, 'product6-620435ae542a9.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` double NOT NULL,
  `created_at` datetime NOT NULL,
  `cart_cmd` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:array)',
  `status` tinyint(1) NOT NULL,
  `terminer` tinyint(1) NOT NULL,
  `payer` tinyint(1) DEFAULT NULL,
  `datedepaimenet` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6EEAA67D67B3B43D` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commande_article`
--

DROP TABLE IF EXISTS `commande_article`;
CREATE TABLE IF NOT EXISTS `commande_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commande_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F4817CC682EA2E54` (`commande_id`),
  KEY `IDX_F4817CC64584665A` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9474526C4F34D596` (`ad_id`),
  KEY `IDX_9474526CF675F31B` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fonction_montre`
--

DROP TABLE IF EXISTS `fonction_montre`;
CREATE TABLE IF NOT EXISTS `fonction_montre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fonction_montre`
--

INSERT INTO `fonction_montre` (`id`, `name`) VALUES
(1, 'Chronographe'),
(2, 'Multifonctions');

-- --------------------------------------------------------

--
-- Structure de la table `forme`
--

DROP TABLE IF EXISTS `forme`;
CREATE TABLE IF NOT EXISTS `forme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9EBAEA612469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `forme`
--

INSERT INTO `forme` (`id`, `category_id`, `name`) VALUES
(1, 1, 'aviator'),
(2, 1, 'carré'),
(3, 1, 'masque'),
(4, 1, 'papillon'),
(5, 1, 'rectangulaire'),
(6, 1, 'round'),
(7, 1, 'wayfarer');

-- --------------------------------------------------------

--
-- Structure de la table `forme_du_cadran`
--

DROP TABLE IF EXISTS `forme_du_cadran`;
CREATE TABLE IF NOT EXISTS `forme_du_cadran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_47DAD56712469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `forme_du_cadran`
--

INSERT INTO `forme_du_cadran` (`id`, `name`, `category_id`) VALUES
(1, 'Carré', 2),
(2, 'Round', 2),
(3, 'Rectangle', 2),
(4, 'Ovales', 2),
(5, 'Coussin', 2),
(6, 'Tonneau', 2);

-- --------------------------------------------------------

--
-- Structure de la table `fragrance_de_parfum`
--

DROP TABLE IF EXISTS `fragrance_de_parfum`;
CREATE TABLE IF NOT EXISTS `fragrance_de_parfum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fragrance_de_parfum`
--

INSERT INTO `fragrance_de_parfum` (`id`, `name`) VALUES
(5, 'Eau de parfum'),
(6, 'Eau de parfum'),
(7, 'Eau de cologne '),
(8, 'Eau Fraiche ');

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produits_id` int(11) DEFAULT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E01FBE6ACD11A2CF` (`produits_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `marque`
--

DROP TABLE IF EXISTS `marque`;
CREATE TABLE IF NOT EXISTS `marque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A6F91CE12469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `marque`
--

INSERT INTO `marque` (`id`, `name`, `slug`, `category_id`) VALUES
(1, 'Ray Ban', 'ray-ban', 1),
(2, 'Gucci', 'gucci', 1),
(3, 'Yves Saint Laurent', 'yves-saint-laurent', 1),
(4, 'Miu miu', 'miu-miu', 1),
(5, 'Dita von teese', 'dita-von-teese', 1),
(6, 'Valentino', 'valentino', 1),
(7, 'Louis Vuitton', 'louis-vuitton', 1),
(8, 'Fendi', 'fendi', 1),
(9, 'Cartier', 'cartier', 1),
(10, 'Tom Ford', 'tom-ford', 1),
(11, 'Prada', 'prada', 1),
(12, 'Carrera', 'carrera', 1),
(13, 'Dior', 'dior', 1),
(14, 'Chloé', 'chloe', 1),
(15, 'Guess', 'guess', 2),
(16, 'Guess collection', 'guess-collection', 2),
(17, 'Armani', 'armani', 2),
(18, 'Fossil', 'fossil', 2),
(19, 'Festina', 'festina', 2),
(20, 'Daniel Wellington', 'daniel', 2),
(21, 'Marc jacobs', 'marc-jacobs', 2),
(22, 'Hugo Boss', 'hugo-boss', 2),
(23, 'Calvin klein', 'calvin-klein', 2),
(24, 'Curren', 'curren', 2),
(25, 'Michael kros', 'michael-kros', 2),
(26, 'Casio', 'casio', 2);

-- --------------------------------------------------------

--
-- Structure de la table `matieres_du_lunette`
--

DROP TABLE IF EXISTS `matieres_du_lunette`;
CREATE TABLE IF NOT EXISTS `matieres_du_lunette` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matieres_du_lunette`
--

INSERT INTO `matieres_du_lunette` (`id`, `name`) VALUES
(1, 'Métal'),
(2, 'Plastique'),
(3, 'Bois');

-- --------------------------------------------------------

--
-- Structure de la table `matiere_du_branche`
--

DROP TABLE IF EXISTS `matiere_du_branche`;
CREATE TABLE IF NOT EXISTS `matiere_du_branche` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matiere_du_branche`
--

INSERT INTO `matiere_du_branche` (`id`, `name`) VALUES
(1, 'Métal'),
(2, 'Plastique'),
(3, 'Bois');

-- --------------------------------------------------------

--
-- Structure de la table `matier_bracelet`
--

DROP TABLE IF EXISTS `matier_bracelet`;
CREATE TABLE IF NOT EXISTS `matier_bracelet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3F8753B012469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matier_bracelet`
--

INSERT INTO `matier_bracelet` (`id`, `name`, `category_id`) VALUES
(1, 'Acier', 2),
(2, 'Ceramique', 2),
(3, 'Silicone', 2),
(4, 'Tissu', 2),
(5, 'cuir', 2);

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `newsletter`
--

DROP TABLE IF EXISTS `newsletter`;
CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `plaquettes_de_nez`
--

DROP TABLE IF EXISTS `plaquettes_de_nez`;
CREATE TABLE IF NOT EXISTS `plaquettes_de_nez` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `plaquettes_de_nez`
--

INSERT INTO `plaquettes_de_nez` (`id`, `name`) VALUES
(1, 'Non Ajustable'),
(2, 'Ajustable');

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categories_id` int(11) DEFAULT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `marque_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promo` tinyint(1) NOT NULL,
  `price_promo` double DEFAULT NULL,
  `newprice` double DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `slug` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activer` tinyint(1) NOT NULL DEFAULT '1',
  `sex` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `refrence` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_id` int(11) DEFAULT NULL,
  `forme_id` int(11) DEFAULT NULL,
  `cadres_id` int(11) DEFAULT NULL,
  `sub_sub_categories_id` int(11) DEFAULT NULL,
  `matier_bracelet_id` int(11) DEFAULT NULL,
  `type_du_mouvement_id` int(11) DEFAULT NULL,
  `fonction_montre_id` int(11) DEFAULT NULL,
  `forme_du_cadran_id` int(11) DEFAULT NULL,
  `verre_de_montre_id` int(11) DEFAULT NULL,
  `plaquettes_de_nez_id` int(11) DEFAULT NULL,
  `matieres_du_lunette_id` int(11) DEFAULT NULL,
  `matiere_du_branche_id` int(11) DEFAULT NULL,
  `volume_id` int(11) DEFAULT NULL,
  `type_de_maquillage_id` int(11) DEFAULT NULL,
  `fragrance_de_parfum_id` int(11) DEFAULT NULL,
  `link_youtube` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D34A04ADA21214B7` (`categories_id`),
  KEY `IDX_D34A04ADF7BFE87C` (`sub_category_id`),
  KEY `IDX_D34A04AD4827B9B2` (`marque_id`),
  KEY `IDX_D34A04ADBACD6074` (`style_id`),
  KEY `IDX_D34A04ADBCE84E7C` (`forme_id`),
  KEY `IDX_D34A04AD17ED26BB` (`cadres_id`),
  KEY `IDX_D34A04AD2FA68083` (`sub_sub_categories_id`),
  KEY `IDX_D34A04AD4155230B` (`matier_bracelet_id`),
  KEY `IDX_D34A04AD3F978C32` (`type_du_mouvement_id`),
  KEY `IDX_D34A04ADE2A2F0A8` (`fonction_montre_id`),
  KEY `IDX_D34A04ADB1901242` (`forme_du_cadran_id`),
  KEY `IDX_D34A04AD4C2316BA` (`verre_de_montre_id`),
  KEY `IDX_D34A04ADD048518F` (`plaquettes_de_nez_id`),
  KEY `IDX_D34A04ADBBC22B95` (`matieres_du_lunette_id`),
  KEY `IDX_D34A04AD76EAA01E` (`matiere_du_branche_id`),
  KEY `IDX_D34A04AD8FD80EEA` (`volume_id`),
  KEY `IDX_D34A04ADC5461E64` (`type_de_maquillage_id`),
  KEY `IDX_D34A04AD1246DFE` (`fragrance_de_parfum_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reset_password`
--

DROP TABLE IF EXISTS `reset_password`;
CREATE TABLE IF NOT EXISTS `reset_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B9983CE5A76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `title`) VALUES
(1, 'ROLE_ADMIN'),
(2, 'ROLE_USER');

-- --------------------------------------------------------

--
-- Structure de la table `roles_user`
--

DROP TABLE IF EXISTS `roles_user`;
CREATE TABLE IF NOT EXISTS `roles_user` (
  `roles_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`roles_id`,`user_id`),
  KEY `IDX_57048B3038C751C4` (`roles_id`),
  KEY `IDX_57048B30A76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles_user`
--

INSERT INTO `roles_user` (`roles_id`, `user_id`) VALUES
(1, 1),
(1, 8);

-- --------------------------------------------------------

--
-- Structure de la table `style`
--

DROP TABLE IF EXISTS `style`;
CREATE TABLE IF NOT EXISTS `style` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_33BDB86A12469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `style`
--

INSERT INTO `style` (`id`, `category_id`, `name`) VALUES
(1, 1, 'basique'),
(2, 1, 'casual'),
(3, 1, 'Classique'),
(4, 1, 'girlay'),
(5, 1, 'habillé'),
(6, 2, 'Basique'),
(7, 2, 'Casual'),
(8, 2, 'Classique'),
(9, 2, 'Enfant'),
(10, 2, 'Habillé'),
(11, 2, 'Sport');

-- --------------------------------------------------------

--
-- Structure de la table `subcategory`
--

DROP TABLE IF EXISTS `subcategory`;
CREATE TABLE IF NOT EXISTS `subcategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DDCA44812469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `subcategory`
--

INSERT INTO `subcategory` (`id`, `category_id`, `name`, `slug`, `picture`) VALUES
(1, 1, 'Lunettes De soleil', 'lunettes-de-soleil', 'category1-620ac090f2f82.jpg'),
(4, 1, 'Cadres Optiques', 'cadres-optiques', 'product9-620ac21c13cca.jpg'),
(5, 2, 'Montres Pour Hommes', 'montres-pour-hommes', 'cat2-620ac27203863.jpg'),
(6, 2, 'Montres Pour Femmes', 'montres-pour-femmes', 'cat1-620ac281253a4.jpg'),
(7, 2, 'Montres Pour Enfants', 'montres-pour-enfants', 'cat5-620ac3b39da82.jpg'),
(8, 2, 'Smartwatches', 'smartwatches', 'product17-620ac2f312d1e.jpg'),
(10, 3, 'Parfums', 'parfums', 'product18-620ac2feedc66.jpg'),
(11, 3, 'Maquillage', 'maquillage', 'product23-620ac460b2038.jpg'),
(12, 3, 'Appareils', 'appareils', 'product2-620ac59ac54f9.jpg'),
(13, 6, 'Sac À Main', 'sac-a-main', 'product11-620ac4913a4c1.jpg'),
(14, 6, 'Bijoux', 'bijoux', 'cat7-620ac4e9167ae.png'),
(15, 6, 'Articles Pour Cadeaux', 'articles-pour-cadeaux', 'cat8-620ac53571958.jpg'),
(16, 6, 'Portefeuilles', 'portefeuilles', 'product11-620ac5c7a2a36.jpg'),
(17, 6, 'Sac pour homme', 'sac-pour-homme', 'product42-620ac6013ca8a.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `sub_sub_categories`
--

DROP TABLE IF EXISTS `sub_sub_categories`;
CREATE TABLE IF NOT EXISTS `sub_sub_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sub_sub_categories`
--

INSERT INTO `sub_sub_categories` (`id`, `name`, `slug`, `picture`) VALUES
(1, 'Lunettes Homme', 'lunettes-homme', 'cat2-6212503647349.jpg'),
(2, 'Lunettes Femme', 'lunettes-femme', 'cat1-621250410f6b7.jpg'),
(3, 'Parfums Pour Hommes', 'parfums-pour-hommes', 'parfumhome-621aa1f164f7d.png'),
(4, 'Parfums Pour Femmes', 'parfums-pour-femmes', 'parfumfemme621aa21786961-621aa5188c03e.png'),
(5, 'Parfums Pour Enfants', 'parfums-pour-enfants', 'images-621aa5aec778c.jpg'),
(6, 'Lèvres', 'levres', 'rouge-621aa6749cb94.png'),
(7, 'Ongles', 'ongles', 'ongle-621aa6cbbf2b4.png'),
(8, 'Palettes & Coffret', 'palettes-coffret', 'product1-621aa765339eb.png'),
(9, 'Pinceaux & Accessoires', 'pinceaux-accessoires', '4460b85c0ea0b8b9a24f2002df7cc3cf-621aa7b6b924d.png'),
(10, 'Visage', 'visage', '93259-621aa858a60cc.png'),
(11, 'Yeux', 'yeux', 'f31450db358e35f6c683b6525421de11-621aa8c1c36c5.png'),
(12, 'Appareils Pour Hommes', 'appareils-pour-hommes', 'sweetlfrasoirelectriquehommerechargeable3dte-621aa9453a46b.png'),
(13, 'Appareils Pour Femmes', 'appareils-pour-femmes', 'telechargement-621aa98892fa9.png');

-- --------------------------------------------------------

--
-- Structure de la table `sub_sub_categories_subcategory`
--

DROP TABLE IF EXISTS `sub_sub_categories_subcategory`;
CREATE TABLE IF NOT EXISTS `sub_sub_categories_subcategory` (
  `sub_sub_categories_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  PRIMARY KEY (`sub_sub_categories_id`,`subcategory_id`),
  KEY `IDX_ADA5E55A2FA68083` (`sub_sub_categories_id`),
  KEY `IDX_ADA5E55A5DC6FE57` (`subcategory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sub_sub_categories_subcategory`
--

INSERT INTO `sub_sub_categories_subcategory` (`sub_sub_categories_id`, `subcategory_id`) VALUES
(1, 1),
(1, 4),
(2, 1),
(2, 4),
(3, 10),
(4, 10),
(5, 10),
(6, 11),
(7, 11),
(8, 11),
(9, 11),
(10, 11),
(11, 11),
(12, 12),
(13, 12);

-- --------------------------------------------------------

--
-- Structure de la table `type_de_maquillage`
--

DROP TABLE IF EXISTS `type_de_maquillage`;
CREATE TABLE IF NOT EXISTS `type_de_maquillage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_77742E512469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `type_de_maquillage`
--

INSERT INTO `type_de_maquillage` (`id`, `category_id`, `name`) VALUES
(1, 3, 'Curl And Volume Mascara'),
(2, 3, 'Volume Mascara'),
(3, 3, 'False Lash Effect'),
(4, 3, 'Lash Lifting'),
(5, 3, 'Clump Free'),
(6, 3, 'Proof');

-- --------------------------------------------------------

--
-- Structure de la table `type_du_mouvement`
--

DROP TABLE IF EXISTS `type_du_mouvement`;
CREATE TABLE IF NOT EXISTS `type_du_mouvement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ADF0E0F912469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `type_du_mouvement`
--

INSERT INTO `type_du_mouvement` (`id`, `category_id`, `name`) VALUES
(1, 2, 'Quatrz'),
(2, 2, 'Quatrz solaire'),
(3, 2, 'Quatrz kinétique'),
(4, 2, 'Manuel'),
(5, 2, 'Automatique ');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adress` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reset_token` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codepostal` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `picture`, `hash`, `adress`, `tel`, `reset_token`, `codepostal`) VALUES
(1, 'marwen', 'mdoukhi', 'marwenmdoukhi@gmail.com', 'https://avatars.io/twitter/liiorC', '$2y$13$Vwx69Ns4KpLorxPcrOeyb.e3FGQdJwqzKVpF3efo4t4ms.9e0ZKwG', 'marsa aa', '28157090', NULL, 1000),
(8, 'Dali ', 'Omar', 'daliOmar@gmail.com', 'https://avatars.io/twitter/liiorC', '$2y$13$Vwx69Ns4KpLorxPcrOeyb.e3FGQdJwqzKVpF3efo4t4ms.9e0ZKwG', 'marsa aa', '28157090', NULL, 1000);

-- --------------------------------------------------------

--
-- Structure de la table `verre_de_montre`
--

DROP TABLE IF EXISTS `verre_de_montre`;
CREATE TABLE IF NOT EXISTS `verre_de_montre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `verre_de_montre`
--

INSERT INTO `verre_de_montre` (`id`, `name`) VALUES
(1, 'Minéral'),
(2, 'Acrylique'),
(3, 'Saphir');

-- --------------------------------------------------------

--
-- Structure de la table `volume`
--

DROP TABLE IF EXISTS `volume`;
CREATE TABLE IF NOT EXISTS `volume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B99ACDDE12469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `volume`
--

INSERT INTO `volume` (`id`, `category_id`, `name`) VALUES
(1, 3, '25 ml'),
(2, 3, '30 ml'),
(3, 3, '35 ml'),
(4, 3, '40 ml'),
(5, 3, '45 ml'),
(6, 3, '50 ml'),
(7, 3, '60 ml'),
(8, 3, '65 ml'),
(9, 3, '70 ml'),
(10, 3, '75 ml'),
(11, 3, '80 ml'),
(12, 3, '90 ml'),
(13, 3, '100 ml'),
(14, 3, '110 ml'),
(15, 3, '120 ml'),
(16, 3, '125 ml'),
(17, 3, '150 ml'),
(18, 3, '175 ml'),
(19, 3, '200 ml'),
(20, 3, '236 ml'),
(21, 3, '300 ml'),
(22, 3, '400 ml'),
(23, 3, '500 ml'),
(24, 3, '1 l');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cadre`
--
ALTER TABLE `cadre`
  ADD CONSTRAINT `FK_F42587B912469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `FK_6EEAA67D67B3B43D` FOREIGN KEY (`users_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `commande_article`
--
ALTER TABLE `commande_article`
  ADD CONSTRAINT `FK_F4817CC64584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_F4817CC682EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`);

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C4F34D596` FOREIGN KEY (`ad_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_9474526CF675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `forme`
--
ALTER TABLE `forme`
  ADD CONSTRAINT `FK_9EBAEA612469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `forme_du_cadran`
--
ALTER TABLE `forme_du_cadran`
  ADD CONSTRAINT `FK_47DAD56712469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `FK_E01FBE6ACD11A2CF` FOREIGN KEY (`produits_id`) REFERENCES `product` (`id`);

--
-- Contraintes pour la table `marque`
--
ALTER TABLE `marque`
  ADD CONSTRAINT `FK_5A6F91CE12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `matier_bracelet`
--
ALTER TABLE `matier_bracelet`
  ADD CONSTRAINT `FK_3F8753B012469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_D34A04AD1246DFE` FOREIGN KEY (`fragrance_de_parfum_id`) REFERENCES `fragrance_de_parfum` (`id`),
  ADD CONSTRAINT `FK_D34A04AD17ED26BB` FOREIGN KEY (`cadres_id`) REFERENCES `cadre` (`id`),
  ADD CONSTRAINT `FK_D34A04AD2FA68083` FOREIGN KEY (`sub_sub_categories_id`) REFERENCES `sub_sub_categories` (`id`),
  ADD CONSTRAINT `FK_D34A04AD3F978C32` FOREIGN KEY (`type_du_mouvement_id`) REFERENCES `type_du_mouvement` (`id`),
  ADD CONSTRAINT `FK_D34A04AD4155230B` FOREIGN KEY (`matier_bracelet_id`) REFERENCES `matier_bracelet` (`id`),
  ADD CONSTRAINT `FK_D34A04AD4827B9B2` FOREIGN KEY (`marque_id`) REFERENCES `marque` (`id`),
  ADD CONSTRAINT `FK_D34A04AD4C2316BA` FOREIGN KEY (`verre_de_montre_id`) REFERENCES `verre_de_montre` (`id`),
  ADD CONSTRAINT `FK_D34A04AD76EAA01E` FOREIGN KEY (`matiere_du_branche_id`) REFERENCES `matiere_du_branche` (`id`),
  ADD CONSTRAINT `FK_D34A04AD8FD80EEA` FOREIGN KEY (`volume_id`) REFERENCES `volume` (`id`),
  ADD CONSTRAINT `FK_D34A04ADA21214B7` FOREIGN KEY (`categories_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_D34A04ADB1901242` FOREIGN KEY (`forme_du_cadran_id`) REFERENCES `forme_du_cadran` (`id`),
  ADD CONSTRAINT `FK_D34A04ADBACD6074` FOREIGN KEY (`style_id`) REFERENCES `style` (`id`),
  ADD CONSTRAINT `FK_D34A04ADBBC22B95` FOREIGN KEY (`matieres_du_lunette_id`) REFERENCES `matieres_du_lunette` (`id`),
  ADD CONSTRAINT `FK_D34A04ADBCE84E7C` FOREIGN KEY (`forme_id`) REFERENCES `forme` (`id`),
  ADD CONSTRAINT `FK_D34A04ADC5461E64` FOREIGN KEY (`type_de_maquillage_id`) REFERENCES `type_de_maquillage` (`id`),
  ADD CONSTRAINT `FK_D34A04ADD048518F` FOREIGN KEY (`plaquettes_de_nez_id`) REFERENCES `plaquettes_de_nez` (`id`),
  ADD CONSTRAINT `FK_D34A04ADE2A2F0A8` FOREIGN KEY (`fonction_montre_id`) REFERENCES `fonction_montre` (`id`),
  ADD CONSTRAINT `FK_D34A04ADF7BFE87C` FOREIGN KEY (`sub_category_id`) REFERENCES `subcategory` (`id`);

--
-- Contraintes pour la table `reset_password`
--
ALTER TABLE `reset_password`
  ADD CONSTRAINT `FK_B9983CE5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `roles_user`
--
ALTER TABLE `roles_user`
  ADD CONSTRAINT `FK_57048B3038C751C4` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_57048B30A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `style`
--
ALTER TABLE `style`
  ADD CONSTRAINT `FK_33BDB86A12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `subcategory`
--
ALTER TABLE `subcategory`
  ADD CONSTRAINT `FK_DDCA44812469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `sub_sub_categories_subcategory`
--
ALTER TABLE `sub_sub_categories_subcategory`
  ADD CONSTRAINT `FK_ADA5E55A2FA68083` FOREIGN KEY (`sub_sub_categories_id`) REFERENCES `sub_sub_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_ADA5E55A5DC6FE57` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategory` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `type_de_maquillage`
--
ALTER TABLE `type_de_maquillage`
  ADD CONSTRAINT `FK_77742E512469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `type_du_mouvement`
--
ALTER TABLE `type_du_mouvement`
  ADD CONSTRAINT `FK_ADF0E0F912469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `volume`
--
ALTER TABLE `volume`
  ADD CONSTRAINT `FK_B99ACDDE12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
