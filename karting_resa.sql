-- Base de données : `karting`

DROP DATABASE IF EXISTS `karting`;

CREATE DATABASE IF NOT EXISTS `karting`;

USE `karting`;


-- TABLES (STRUCTURE)

-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 09 mars 2026 à 10:23
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Structure de la table `opening_hours`
--

DROP TABLE IF EXISTS `opening_hours`;
CREATE TABLE IF NOT EXISTS `opening_hours` (
  `oh_id` int NOT NULL AUTO_INCREMENT,
  `oh_day` tinyint NOT NULL COMMENT '0=lundi, 1=mardi, ..., 6=dimanche',
  `oh_open` time DEFAULT NULL COMMENT 'NULL = fermé ce jour',
  `oh_close` time DEFAULT NULL,
  PRIMARY KEY (`oh_id`),
  UNIQUE KEY `uq_oh_day` (`oh_day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `special_hours`
--

DROP TABLE IF EXISTS `special_hours`;
CREATE TABLE IF NOT EXISTS `special_hours` (
  `sh_id` int NOT NULL AUTO_INCREMENT,
  `sh_label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ex: Vacances de Noël',
  `sh_date_start` date NOT NULL,
  `sh_date_end` date NOT NULL,
  `sh_day` tinyint NOT NULL COMMENT '0=lundi, ..., 6=dimanche',
  `sh_open` time DEFAULT NULL COMMENT 'NULL = fermé ce jour pendant la période',
  `sh_close` time DEFAULT NULL,
  PRIMARY KEY (`sh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `rol_id` int NOT NULL AUTO_INCREMENT,
  `rol_name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`rol_id`, `rol_name`, `rol_description`, `rol_created_at`) VALUES
(1, 'admin', 'Administrateur avec tous les privilèges', '2026-03-03 13:35:37'),
(2, 'user', 'Utilisateur standard avec accès limité', '2026-03-03 13:35:37');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `usr_id` int NOT NULL AUTO_INCREMENT,
  `usr_role_id` int NOT NULL,
  `usr_firstname` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_lastname` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_phonenumber` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `usr_updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`usr_id`),
  KEY `fk_user_role` (`usr_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`usr_id`, `usr_role_id`, `usr_firstname`, `usr_lastname`, `usr_email`, `usr_password`, `usr_phonenumber`, `usr_created_at`, `usr_updated_at`) VALUES
(1, 1, 'Admin', 'Système', 'admin@88karting.com', '$2y$10$tHXpOT82.6wTeLj12ziiNeCNaj1q4OCGQe6e7kgUbFHSp7Q6Jg5O2', '0601020304', '2026-03-03 13:35:37', '2026-03-03 13:35:37'),
(2, 2, 'Carole', 'Dupont', 'carole.dupont@email.com', '$2y$10$xUzX.UZBz3TUhaOhh/G68umIn08njxDxym9DS1ENeQdKr45vNKw6S', '0612345678', '2026-03-03 13:35:37', '2026-03-03 13:35:37'),
(3, 2, 'Marie', 'Martin', 'marie.martin@email.com', '$2y$10$uy5pFCKHp77fJsu4HwK50O/zro0YvsSvIyoP0B96jK.57tA2tJ0Te', '0623456789', '2026-03-03 13:35:37', '2026-03-03 13:35:37'),
(4, 2, 'Pierre', 'Leroy', 'pierre.leroy@email.com', '$2y$10$1qEFby09JSAbCGaYs36Z.O1cOTM39mDmPcN7TlXxXiHC/GcUVUjGi', '0734567890', '2026-03-03 13:35:37', '2026-03-03 13:35:37'),
(5, 2, 'Sophie', 'Meyer', 'sophie.meyer@email.com', '$2y$10$O5kv9.iYNc2mJ5oyRa94ZOtVr8dwZALC6GlhhmqRcYq5eM/JuZqCG', '0645678901', '2026-03-03 13:35:37', '2026-03-03 13:35:37');

-- --------------------------------------------------------

--
-- Structure de la table `session`
--

DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
  `ses_id` int NOT NULL AUTO_INCREMENT,
  `ses_track_id` int NOT NULL,
  `ses_start_time` datetime NOT NULL,
  `ses_end_time` datetime NOT NULL,
  `ses_capacity` int NOT NULL,
  `ses_price` decimal(7,2) NOT NULL,
  `ses_session_status` enum('scheduled','ongoing','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `ses_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ses_id`),
  UNIQUE KEY `uq_track_start` (`ses_track_id`,`ses_start_time`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Déchargement des données de la table `session`
--

INSERT INTO `session` (`ses_id`, `ses_track_id`, `ses_start_time`, `ses_end_time`, `ses_capacity`, `ses_price`, `ses_session_status`, `ses_created_at`) VALUES
(83, 2, '2026-03-10 09:00:00', '2026-03-10 09:30:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(84, 2, '2026-03-10 09:30:00', '2026-03-10 10:00:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(85, 2, '2026-03-10 10:00:00', '2026-03-10 10:30:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(86, 2, '2026-03-10 10:30:00', '2026-03-10 11:00:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(87, 2, '2026-03-10 11:00:00', '2026-03-10 11:30:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(88, 2, '2026-03-10 11:30:00', '2026-03-10 12:00:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(89, 2, '2026-03-10 12:00:00', '2026-03-10 12:30:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(90, 2, '2026-03-10 12:30:00', '2026-03-10 13:00:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(91, 2, '2026-03-10 13:00:00', '2026-03-10 13:30:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(92, 2, '2026-03-10 13:30:00', '2026-03-10 14:00:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(93, 2, '2026-03-10 14:00:00', '2026-03-10 14:30:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(94, 2, '2026-03-10 14:30:00', '2026-03-10 15:00:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(95, 2, '2026-03-10 15:00:00', '2026-03-10 15:30:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(96, 2, '2026-03-10 15:30:00', '2026-03-10 16:00:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(97, 2, '2026-03-10 16:00:00', '2026-03-10 16:30:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(98, 2, '2026-03-10 16:30:00', '2026-03-10 17:00:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(99, 2, '2026-03-10 17:00:00', '2026-03-10 17:30:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43'),
(100, 2, '2026-03-10 17:30:00', '2026-03-10 18:00:00', 10, 25.00, 'scheduled', '2026-03-06 14:35:43');

-- --------------------------------------------------------

--
-- Structure de la table `session_vehicle`
--

DROP TABLE IF EXISTS `session_vehicle`;
CREATE TABLE IF NOT EXISTS `session_vehicle` (
  `sv_id` int NOT NULL AUTO_INCREMENT,
  `sv_vehicle_id` int NOT NULL,
  `sv_session_id` int NOT NULL,
  PRIMARY KEY (`sv_id`),
  KEY `fk_session_vehicle_vehicle` (`sv_vehicle_id`),
  KEY `fk_session_vehicle_session` (`sv_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track`
--

DROP TABLE IF EXISTS `track`;
CREATE TABLE IF NOT EXISTS `track` (
  `trk_id` int NOT NULL AUTO_INCREMENT,
  `trk_name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trk_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `trk_length_meters` int DEFAULT NULL,
  `trk_is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`trk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `track`
--

INSERT INTO `track` (`trk_id`, `trk_name`, `trk_description`, `trk_length_meters`, `trk_is_active`) VALUES
(1, 'Lightning Ride', 'Circuit technique avec virages serrés, idéal pour les pilotes expérimentés', 1200, 1),
(2, 'Speedway 88', 'Circuit rapide avec longues lignes droites, parfait pour la vitesse', 1500, 1),
(3, 'Mini Racer Track', 'Circuit adapté aux débutants et aux enfants, sécurisé', 800, 1),
(4, 'Asphalt Challenge', 'Circuit extérieur professionnel avec dénivelé, réservé aux experts', 1800, 1);

-- --------------------------------------------------------

--
-- Structure de la table `vehicle`
--

DROP TABLE IF EXISTS `vehicle`;
CREATE TABLE IF NOT EXISTS `vehicle` (
  `vhc_id` int NOT NULL AUTO_INCREMENT,
  `vhc_name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vhc_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `vhc_max_speed_kmh` int DEFAULT NULL,
  `vhc_is_available` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`vhc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vehicle`
--

INSERT INTO `vehicle` (`vhc_id`, `vhc_name`, `vhc_description`, `vhc_max_speed_kmh`, `vhc_is_available`) VALUES
(1, 'Kart DriftMaster 300', 'Kart haute performance pour pilotes expérimentés', 80, 1),
(2, 'Kart Speedster V8', 'Kart polyvalent pour tous les niveaux', 60, 1),
(3, 'Kart Junior Fun', 'Kart adapté aux enfants de 8 à 14 ans', 40, 1),
(4, 'Kart Électrique Pro', 'Kart électrique silencieux et puissant', 70, 1),
(5, 'Kart Biplace', 'Kart biplace pour partager l\expérience', 50, 1);

-- --------------------------------------------------------

--
-- Structure de la table `booking`
--

DROP TABLE IF EXISTS `booking`;
CREATE TABLE IF NOT EXISTS `booking` (
  `bkg_id` int NOT NULL AUTO_INCREMENT,
  `bkg_user_id` int DEFAULT NULL,
  `bkg_session_id` int NOT NULL,
  `bkg_nb_of_participants` int NOT NULL,
  `bkg_booking_status` enum('pending','confirmed','cancelled','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `bkg_total_price` decimal(7,2) NOT NULL,
  `bkg_booked_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `bkg_updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `bkg_cancelled_at` datetime DEFAULT NULL,
  PRIMARY KEY (`bkg_id`),
  KEY `fk_booking_session` (`bkg_session_id`),
  KEY `fk_booking_user` (`bkg_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `pmt_id` int NOT NULL AUTO_INCREMENT,
  `pmt_booking_id` int NOT NULL,
  `pmt_total_paid` decimal(7,2) NOT NULL,
  `pmt_payment_status` enum('pending','completed','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `pmt_paid_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pmt_id`),
  KEY `fk_payment_booking` (`pmt_booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_session` FOREIGN KEY (`bkg_session_id`) REFERENCES `session` (`ses_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`bkg_user_id`) REFERENCES `user` (`usr_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_booking` FOREIGN KEY (`pmt_booking_id`) REFERENCES `booking` (`bkg_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `fk_session_track` FOREIGN KEY (`ses_track_id`) REFERENCES `track` (`trk_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `session_vehicle`
--
ALTER TABLE `session_vehicle`
  ADD CONSTRAINT `fk_session_vehicle_session` FOREIGN KEY (`sv_session_id`) REFERENCES `session` (`ses_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_session_vehicle_vehicle` FOREIGN KEY (`sv_vehicle_id`) REFERENCES `vehicle` (`vhc_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`usr_role_id`) REFERENCES `role` (`rol_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
