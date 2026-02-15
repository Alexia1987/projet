-- DATABASE : KARTING

DROP DATABASE IF EXISTS `karting`;

CREATE DATABASE IF NOT EXISTS `karting` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `karting`;


-- TABLES (STRUCTURE)

-- TABLE: role
CREATE TABLE `role` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(80) NOT NULL UNIQUE,
    `description` VARCHAR(255),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- TABLE: user
CREATE TABLE `user` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `role_id` INT NOT NULL,
    `firstname` VARCHAR(80) NOT NULL,
    `lastname` VARCHAR(80) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL, 
    `phonenumber` VARCHAR(30),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- TABLE: track
CREATE TABLE `track` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(80) NOT NULL,
    `description` TEXT,
    `length_meters` INT,
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;


-- TABLE: vehicle
CREATE TABLE `vehicle` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(80) NOT NULL,
    `description` TEXT,
    `max_speed_kmh` INT,
    `is_available` BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;


-- TABLE: session
CREATE TABLE `session` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,    
    `track_id` INT NOT NULL,
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NOT NULL,
    `capacity` INT NOT NULL,
    `price` DECIMAL(7,2) NOT NULL,
    `session_status` ENUM('scheduled', 'ongoing', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP 
) ENGINE=InnoDB;


-- TABLE DE LIAISON: session_vehicle
CREATE TABLE `session_vehicle` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `vehicle_id` INT NOT NULL,
    `session_id`INT NOT NULL
) ENGINE=InnoDB;


-- TABLE: booking
CREATE TABLE `booking` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `session_id` INT NOT NULL,  
    `nb_of_participants` INT NOT NULL,      
    `booking_status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    `total_price` DECIMAL(7,2) NOT NULL,
    `booked_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `cancelled_at` DATETIME NULL
) ENGINE=InnoDB;


-- TABLE: payment
CREATE TABLE `payment` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `booking_id` INT NOT NULL UNIQUE,
    `total_paid` DECIMAL(7,2) NOT NULL,
    `payment_status` ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    `paid_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- AJOUT FOREIGN KEYS :

ALTER TABLE `user`
ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`)
REFERENCES `role`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `session`
ADD CONSTRAINT `fk_session_track` FOREIGN KEY (`track_id`)
REFERENCES `track`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `session_vehicle`
ADD CONSTRAINT `fk_session_vehicle_vehicle` FOREIGN KEY (`vehicle_id`)
REFERENCES `vehicle`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `session_vehicle`
ADD CONSTRAINT `fk_session_vehicle_session` FOREIGN KEY (`session_id`)
REFERENCES `session`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `booking`
ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`)
REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `booking`
ADD CONSTRAINT `fk_booking_session` FOREIGN KEY (`session_id`)
REFERENCES `session`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `payment`
ADD CONSTRAINT `fk_payment_booking` FOREIGN KEY (`booking_id`)
REFERENCES `booking`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;


-- DONNÉES TEST

-- rôles
INSERT INTO role (name, description) 
VALUES ('admin', 'Administrateur avec tous les privilèges'),
       ('user', 'Utilisateur standard avec accès limité');


-- utilisateurs 
INSERT INTO user (role_id, email, password, firstname, lastname, phonenumber) 
VALUES (1, 'admin@88karting.com', '$2y$10$tHXpOT82.6wTeLj12ziiNeCNaj1q4OCGQe6e7kgUbFHSp7Q6Jg5O2', 'Admin', 'Système', '0601020304'),
       (2, 'carole.dupont@email.com', '$2y$10$xUzX.UZBz3TUhaOhh/G68umIn08njxDxym9DS1ENeQdKr45vNKw6S', 'Carole', 'Dupont', '0612345678'),
       (2, 'marie.martin@email.com', '$2y$10$uy5pFCKHp77fJsu4HwK50O/zro0YvsSvIyoP0B96jK.57tA2tJ0Te', 'Marie', 'Martin', '0623456789'),
       (2, 'pierre.leroy@email.com', '$2y$10$1qEFby09JSAbCGaYs36Z.O1cOTM39mDmPcN7TlXxXiHC/GcUVUjGi', 'Pierre', 'Leroy', '0734567890'),
       (2, 'sophie.meyer@email.com', '$2y$10$O5kv9.iYNc2mJ5oyRa94ZOtVr8dwZALC6GlhhmqRcYq5eM/JuZqCG', 'Sophie', 'Meyer', '0645678901');

-- circuits
INSERT INTO track (name, description, length_meters, is_active) 
VALUES ('Lightning Ride', 'Circuit technique avec virages serrés, idéal pour les pilotes expérimentés', 1200, TRUE),
       ('Speedway 88', 'Circuit rapide avec longues lignes droites, parfait pour la vitesse', 1500, TRUE),
       ('Mini Racer Track', 'Circuit adapté aux débutants et aux enfants, sécurisé', 800, TRUE),
       ('Asphalt Challenge', 'Circuit extérieur professionnel avec dénivelé, réservé aux experts', 1800, TRUE);

-- véhicules
INSERT INTO vehicle (name, description, max_speed_kmh, is_available) 
VALUES ('Kart DriftMaster 300', 'Kart haute performance pour pilotes expérimentés', 80, TRUE),
       ('Kart Speedster V8', 'Kart polyvalent pour tous les niveaux', 60, TRUE),
       ('Kart Junior Fun', 'Kart adapté aux enfants de 8 à 14 ans', 40, TRUE),
       ('Kart Électrique Pro', 'Kart électrique silencieux et puissant', 70, TRUE),
       ('Kart Biplace', 'Kart biplace pour partager l''expérience', 50, TRUE);

-- sessions 
INSERT INTO session (track_id, start_time, end_time, capacity, price, session_status) 
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
INSERT INTO booking (user_id, session_id, nb_of_participants, booking_status, total_price) VALUES 
-- Réservations confirmées et payées
(2, 1, 2, 'completed', 50.00),
(3, 1, 1, 'completed', 25.00),
(4, 2, 3, 'completed', 60.00),
(5, 3, 2, 'completed', 30.00);




