-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 17 avr. 2025 à 16:01
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
-- Base de données : `clyptor`
--

-- --------------------------------------------------------

--
-- Structure de la table `carpool_bookings`
--

CREATE TABLE `carpool_bookings` (
  `booking_id` int(11) NOT NULL,
  `ride_id` int(11) NOT NULL,
  `passenger_id` int(11) NOT NULL,
  `seats_booked` int(11) NOT NULL DEFAULT 1,
  `booking_status` enum('confirmed','cancelled','completed') DEFAULT 'confirmed',
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `carpool_rides`
--

CREATE TABLE `carpool_rides` (
  `ride_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `departure_address_id` int(11) NOT NULL,
  `destination_address_id` int(11) NOT NULL,
  `departure_datetime` datetime NOT NULL,
  `available_seats` int(11) NOT NULL,
  `price_per_seat` decimal(10,2) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `car_rentals`
--

CREATE TABLE `car_rentals` (
  `rental_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `minimum_rental_days` int(11) DEFAULT 1,
  `available_from` date NOT NULL,
  `available_to` date NOT NULL,
  `pickup_location_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `insurance_included` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('available','rented','unavailable') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `home_booking`
--

CREATE TABLE `home_booking` (
  `booking_id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `booking_status` enum('confirmed','cancelled','completed') DEFAULT 'confirmed',
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `home_rentals`
--

CREATE TABLE `home_rentals` (
  `rental_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `property_type` enum('apartment','house','villa','condo','room') NOT NULL,
  `bedrooms` int(11) NOT NULL,
  `bathrooms` int(11) NOT NULL,
  `max_guests` int(11) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `available_from` date NOT NULL,
  `available_to` date DEFAULT NULL,
  `minimum_stay` int(11) DEFAULT 1,
  `amenities` text DEFAULT NULL,
  `main_photo_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('available','booked','unavailable') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `package_deliveries`
--

CREATE TABLE `package_deliveries` (
  `delivery_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `pickup_address_id` int(11) NOT NULL,
  `delivery_address_id` int(11) NOT NULL,
  `package_description` text NOT NULL,
  `package_weight` decimal(10,2) NOT NULL,
  `package_dimensions` varchar(50) DEFAULT NULL,
  `estimated_value` decimal(10,2) DEFAULT NULL,
  `delivery_deadline` datetime DEFAULT NULL,
  `proposed_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','accepted','in_transit','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `driver_id` int(11) DEFAULT NULL,
  `actual_delivery_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `payment_method` enum('credit_card','paypal','bank_transfer','cash') NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `service_type` enum('carpool','car_rental','home_rental','delivery') NOT NULL,
  `service_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rental_bookings`
--

CREATE TABLE `rental_bookings` (
  `booking_id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL,
  `renter_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `booking_status` enum('confirmed','cancelled','completed') DEFAULT 'confirmed',
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewed_user_id` int(11) NOT NULL,
  `service_type` enum('carpool','car_rental','home_rental','delivery') NOT NULL,
  `related_service_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `verification_token` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating` decimal(3,2) DEFAULT 5.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_verifications`
--

CREATE TABLE `user_verifications` (
  `verification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_type` enum('id_card','driver_license','passport') NOT NULL,
  `document_number` varchar(100) NOT NULL,
  `document_front` varchar(255) NOT NULL,
  `document_back` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `color` varchar(30) NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `vehicle_type` enum('sedan','suv','truck','van','motorcycle') NOT NULL,
  `seat_capacity` int(11) NOT NULL,
  `fuel_type` enum('gasoline','diesel','electric','hybrid') NOT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `carpool_bookings`
--
ALTER TABLE `carpool_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `ride_id` (`ride_id`),
  ADD KEY `passenger_id` (`passenger_id`);

--
-- Index pour la table `carpool_rides`
--
ALTER TABLE `carpool_rides`
  ADD PRIMARY KEY (`ride_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `departure_address_id` (`departure_address_id`),
  ADD KEY `destination_address_id` (`destination_address_id`);

--
-- Index pour la table `car_rentals`
--
ALTER TABLE `car_rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `pickup_location_id` (`pickup_location_id`);

--
-- Index pour la table `home_booking`
--
ALTER TABLE `home_booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `rental_id` (`rental_id`),
  ADD KEY `guest_id` (`guest_id`);

--
-- Index pour la table `home_rentals`
--
ALTER TABLE `home_rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Index pour la table `package_deliveries`
--
ALTER TABLE `package_deliveries`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `pickup_address_id` (`pickup_address_id`),
  ADD KEY `delivery_address_id` (`delivery_address_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Index pour la table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `rental_bookings`
--
ALTER TABLE `rental_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `rental_id` (`rental_id`),
  ADD KEY `renter_id` (`renter_id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewed_user_id` (`reviewed_user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `user_verifications`
--
ALTER TABLE `user_verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `carpool_bookings`
--
ALTER TABLE `carpool_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `carpool_rides`
--
ALTER TABLE `carpool_rides`
  MODIFY `ride_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `car_rentals`
--
ALTER TABLE `car_rentals`
  MODIFY `rental_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `home_booking`
--
ALTER TABLE `home_booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `home_rentals`
--
ALTER TABLE `home_rentals`
  MODIFY `rental_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `package_deliveries`
--
ALTER TABLE `package_deliveries`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rental_bookings`
--
ALTER TABLE `rental_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_verifications`
--
ALTER TABLE `user_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `carpool_bookings`
--
ALTER TABLE `carpool_bookings`
  ADD CONSTRAINT `carpool_bookings_ibfk_1` FOREIGN KEY (`ride_id`) REFERENCES `carpool_rides` (`ride_id`),
  ADD CONSTRAINT `carpool_bookings_ibfk_2` FOREIGN KEY (`passenger_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `carpool_rides`
--
ALTER TABLE `carpool_rides`
  ADD CONSTRAINT `carpool_rides_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `carpool_rides_ibfk_2` FOREIGN KEY (`departure_address_id`) REFERENCES `user_addresses` (`address_id`),
  ADD CONSTRAINT `carpool_rides_ibfk_3` FOREIGN KEY (`destination_address_id`) REFERENCES `user_addresses` (`address_id`);

--
-- Contraintes pour la table `car_rentals`
--
ALTER TABLE `car_rentals`
  ADD CONSTRAINT `car_rentals_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`),
  ADD CONSTRAINT `car_rentals_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `car_rentals_ibfk_3` FOREIGN KEY (`pickup_location_id`) REFERENCES `user_addresses` (`address_id`);

--
-- Contraintes pour la table `home_booking`
--
ALTER TABLE `home_booking`
  DROP FOREIGN KEY `home_booking_ibfk_1`,
  ADD CONSTRAINT `home_booking_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `home_rentals` (`rental_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_booking_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `home_rentals`
--
ALTER TABLE `home_rentals`
  ADD CONSTRAINT `home_rentals_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `home_rentals_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `user_addresses` (`address_id`);

--
-- Contraintes pour la table `package_deliveries`
--
ALTER TABLE `package_deliveries`
  ADD CONSTRAINT `package_deliveries_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `package_deliveries_ibfk_2` FOREIGN KEY (`pickup_address_id`) REFERENCES `user_addresses` (`address_id`),
  ADD CONSTRAINT `package_deliveries_ibfk_3` FOREIGN KEY (`delivery_address_id`) REFERENCES `user_addresses` (`address_id`),
  ADD CONSTRAINT `package_deliveries_ibfk_4` FOREIGN KEY (`driver_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `rental_bookings`
--
ALTER TABLE `rental_bookings`
  ADD CONSTRAINT `rental_bookings_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `car_rentals` (`rental_id`),
  ADD CONSTRAINT `rental_bookings_ibfk_2` FOREIGN KEY (`renter_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`reviewed_user_id`) REFERENCES `users` (`user_id`);

--
-- Contraintes pour la table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_verifications`
--
ALTER TABLE `user_verifications`
  ADD CONSTRAINT `user_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
