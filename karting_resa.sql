-- DATABASE : KARTING

DROP DATABASE IF EXISTS `karting`;

CREATE DATABASE IF NOT EXISTS `karting`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `karting`;


-- TABLES (STRUCTURE)

-- TABLE: role
CREATE TABLE `role` (
  `rol_id` int NOT NULL AUTO_INCREMENT,
  `rol_name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rol_id`)
) ENGINE=InnoDB;


-- TABLE: user
CREATE TABLE `user` (
  `usr_id` int NOT NULL AUTO_INCREMENT,
  `usr_role_id` int NOT NULL,
  `usr_firstname` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_lastname` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_phonenumber` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `usr_updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`usr_id`)
) ENGINE=InnoDB;


-- TABLE: track
CREATE TABLE `track` (
  `trk_id` int NOT NULL AUTO_INCREMENT,
  `trk_name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trk_description` text COLLATE utf8mb4_unicode_ci,
  `trk_length_meters` int DEFAULT NULL,
  `trk_is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`trk_id`)
) ENGINE=InnoDB;


-- TABLE: vehicle
CREATE TABLE `vehicle` (
  `vhc_id` int NOT NULL AUTO_INCREMENT,
  `vhc_name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vhc_description` text COLLATE utf8mb4_unicode_ci,
  `vhc_max_speed_kmh` int DEFAULT NULL,
  `vhc_is_available` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`vhc_id`)
) ENGINE=InnoDB;


-- TABLE: session
CREATE TABLE `session` (
  `ses_id` int NOT NULL AUTO_INCREMENT,
  `ses_track_id` int NOT NULL,
  `ses_start_time` datetime NOT NULL,
  `ses_end_time` datetime NOT NULL,
  `ses_capacity` int NOT NULL,
  `ses_price` decimal(7,2) NOT NULL,
  `ses_session_status` enum('scheduled','ongoing','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `ses_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ses_id`)
) ENGINE=InnoDB;


-- TABLE DE LIAISON: session_vehicle
CREATE TABLE `session_vehicle` (
    `sv_id` INT NOT NULL AUTO_INCREMENT,
    `sv_vehicle_id` INT NOT NULL,
    `sv_session_id` INT NOT NULL,
    PRIMARY KEY (`sv_id`)
) ENGINE=InnoDB;


-- TABLE: booking
CREATE TABLE `booking` (
  `bkg_id` int NOT NULL AUTO_INCREMENT,
  `bkg_user_id` int NOT NULL,
  `bkg_session_id` int NOT NULL,
  `bkg_nb_of_participants` int NOT NULL,
  `bkg_booking_status` enum('pending','confirmed','cancelled','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `bkg_total_price` decimal(7,2) NOT NULL,
  `bkg_booked_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `bkg_updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `bkg_cancelled_at` datetime DEFAULT NULL,
  PRIMARY KEY (`bkg_id`)
) ENGINE=InnoDB;


-- TABLE: payment
CREATE TABLE `payment` (
  `pmt_id` int NOT NULL AUTO_INCREMENT,
  `pmt_booking_id` int NOT NULL,
  `pmt_total_paid` decimal(7,2) NOT NULL,
  `pmt_payment_status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `pmt_paid_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pmt_id`)
) ENGINE=InnoDB;


-- AJOUT FOREIGN KEYS :

ALTER TABLE `user`
ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`usr_role_id`)
REFERENCES `role`(`rol_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `session`
ADD CONSTRAINT `fk_session_track` FOREIGN KEY (`ses_track_id`)
REFERENCES `track`(`trk_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `session_vehicle`
ADD CONSTRAINT `fk_session_vehicle_vehicle` FOREIGN KEY (`sv_vehicle_id`)
REFERENCES `vehicle`(`vhc_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `session_vehicle`
ADD CONSTRAINT `fk_session_vehicle_session` FOREIGN KEY (`sv_session_id`)
REFERENCES `session`(`ses_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `booking`
ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`bkg_user_id`)
REFERENCES `user`(`usr_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `booking`
ADD CONSTRAINT `fk_booking_session` FOREIGN KEY (`bkg_session_id`)
REFERENCES `session`(`ses_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `payment`
ADD CONSTRAINT `fk_payment_booking` FOREIGN KEY (`pmt_booking_id`)
REFERENCES `booking`(`bkg_id`) ON DELETE RESTRICT ON UPDATE CASCADE;


-- DONNÉES TEST

-- rôles
INSERT INTO role (rol_name, rol_description)
VALUES ('admin', 'Administrateur avec tous les privilèges'),
       ('user', 'Utilisateur standard avec accès limité');


-- utilisateurs
INSERT INTO user (usr_role_id, usr_email, usr_password, usr_firstname, usr_lastname, usr_phonenumber)
VALUES (1, 'admin@88karting.com', '$2y$10$tHXpOT82.6wTeLj12ziiNeCNaj1q4OCGQe6e7kgUbFHSp7Q6Jg5O2', 'Admin', 'Système', '0601020304'),
       (2, 'carole.dupont@email.com', '$2y$10$xUzX.UZBz3TUhaOhh/G68umIn08njxDxym9DS1ENeQdKr45vNKw6S', 'Carole', 'Dupont', '0612345678'),
       (2, 'marie.martin@email.com', '$2y$10$uy5pFCKHp77fJsu4HwK50O/zro0YvsSvIyoP0B96jK.57tA2tJ0Te', 'Marie', 'Martin', '0623456789'),
       (2, 'pierre.leroy@email.com', '$2y$10$1qEFby09JSAbCGaYs36Z.O1cOTM39mDmPcN7TlXxXiHC/GcUVUjGi', 'Pierre', 'Leroy', '0734567890'),
       (2, 'sophie.meyer@email.com', '$2y$10$O5kv9.iYNc2mJ5oyRa94ZOtVr8dwZALC6GlhhmqRcYq5eM/JuZqCG', 'Sophie', 'Meyer', '0645678901');

-- circuits
INSERT INTO track (trk_name, trk_description, trk_length_meters, trk_is_active)
VALUES ('Lightning Ride', 'Circuit technique avec virages serrés, idéal pour les pilotes expérimentés', 1200, TRUE),
       ('Speedway 88', 'Circuit rapide avec longues lignes droites, parfait pour la vitesse', 1500, TRUE),
       ('Mini Racer Track', 'Circuit adapté aux débutants et aux enfants, sécurisé', 800, TRUE),
       ('Asphalt Challenge', 'Circuit extérieur professionnel avec dénivelé, réservé aux experts', 1800, TRUE);

-- véhicules
INSERT INTO vehicle (vhc_name, vhc_description, vhc_max_speed_kmh, vhc_is_available)
VALUES ('Kart DriftMaster 300', 'Kart haute performance pour pilotes expérimentés', 80, TRUE),
       ('Kart Speedster V8', 'Kart polyvalent pour tous les niveaux', 60, TRUE),
       ('Kart Junior Fun', 'Kart adapté aux enfants de 8 à 14 ans', 40, TRUE),
       ('Kart Électrique Pro', 'Kart électrique silencieux et puissant', 70, TRUE),
       ('Kart Biplace', 'Kart biplace pour partager l''expérience', 50, TRUE);

-- sessions
INSERT INTO session (ses_track_id, ses_start_time, ses_end_time, ses_capacity, ses_price, ses_session_status)
VALUES
-- Samedi 6 décembre 2025
(1, '2025-12-06 10:00:00', '2025-12-06 10:30:00', 10, 25.00, 'completed'),
(2, '2025-12-06 11:00:00', '2025-12-06 11:30:00', 12, 20.00, 'completed'),
(3, '2025-12-06 14:00:00', '2025-12-06 14:30:00', 8, 15.00, 'completed'),
(4, '2025-12-06 15:00:00', '2025-12-06 15:30:00', 10, 30.00, 'completed'),
(1, '2025-12-06 16:00:00', '2025-12-06 16:30:00', 10, 28.00, 'completed'),

-- Dimanche 7 décembre 2025
(1, '2025-12-07 10:00:00', '2025-12-07 10:30:00', 12, 20.00, 'completed'),
(3, '2025-12-07 11:00:00', '2025-12-07 11:30:00', 8, 15.00, 'completed'),
(2, '2025-12-07 14:00:00', '2025-12-07 14:30:00', 10, 25.00, 'cancelled'),
(1, '2025-12-07 15:00:00', '2025-12-07 15:30:00', 6, 35.00, 'completed'),
(4, '2025-12-07 16:00:00', '2025-12-07 16:30:00', 10, 28.00, 'completed');

--  réservations
INSERT INTO booking (bkg_user_id, bkg_session_id, bkg_nb_of_participants, bkg_booking_status, bkg_total_price) VALUES
-- Réservations confirmées et payées
(2, 1, 2, 'completed', 50.00),
(3, 1, 1, 'completed', 25.00),
(4, 2, 3, 'completed', 60.00),
(5, 3, 2, 'completed', 30.00);
