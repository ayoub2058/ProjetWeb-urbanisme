-- Create database
CREATE DATABASE IF NOT EXISTS clyptor_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clyptor_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contact Messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'Nouveau',
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Cars table
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    location VARCHAR(100) NOT NULL,
    image VARCHAR(255),
    status ENUM('available', 'reserved', 'rented', 'maintenance') DEFAULT 'available',
    owner_id INT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Homes table
CREATE TABLE IF NOT EXISTS homes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    location VARCHAR(100) NOT NULL,
    bedrooms INT NOT NULL,
    bathrooms INT NOT NULL,
    area DECIMAL(10, 2),
    image VARCHAR(255),
    status ENUM('available', 'reserved', 'rented') DEFAULT 'available',
    owner_id INT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Carpooling table
CREATE TABLE IF NOT EXISTS carpooling (
    id INT AUTO_INCREMENT PRIMARY KEY,
    departure VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_date DATETIME NOT NULL,
    available_seats INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    notes TEXT,
    driver_id INT NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_type ENUM('car', 'home', 'carpool') NOT NULL,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    read_status TINYINT(1) DEFAULT 0,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Testimonials table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sample admin user (password: admin123)
INSERT INTO users (username, email, password, is_admin)
VALUES ('admin', 'admin@clyptor.com', '$2y$10$5FDQVd.lYcLSK5e6nSO.yOWHy4LGSGcOhk0e41UMxqRYQxpetrpnq', 1);

-- Sample regular user (password: user123)
INSERT INTO users (username, email, password)
VALUES ('user', 'user@example.com', '$2y$10$S/QIQf5JHsWBkAEaj5TYh.b/qAQgeAqV89.YjJxKRDGVWcgUZZmBC');

-- Sample cars
INSERT INTO cars (title, description, price, location, image, owner_id)
VALUES 
('Toyota Corolla 2021', 'Voiture économique en excellent état. Parfaite pour les déplacements en ville.', 45.00, 'Paris', 'toyota-corolla.jpg', 1),
('Mercedes Classe C', 'Berline de luxe avec intérieur en cuir et toutes les options.', 85.00, 'Lyon', 'mercedes-c.jpg', 1);

-- Sample homes
INSERT INTO homes (title, description, price, location, bedrooms, bathrooms, area, image, owner_id)
VALUES 
('Appartement moderne au centre-ville', 'Bel appartement rénové avec vue panoramique, proche de toutes commodités.', 120.00, 'Paris', 2, 1, 65.00, 'apartment-paris.jpg', 1),
('Villa avec piscine', 'Magnifique villa avec piscine privée et grand jardin pour des vacances de rêve.', 250.00, 'Nice', 4, 3, 180.00, 'villa-nice.jpg', 1);

-- Sample carpooling
INSERT INTO carpooling (departure, destination, departure_date, available_seats, price, notes, driver_id)
VALUES 
('Paris', 'Lyon', '2023-08-15 08:00:00', 3, 25.00, 'Départ de la Gare de Lyon. Petite valise seulement.', 1),
('Marseille', 'Nice', '2023-08-20 14:00:00', 2, 15.00, 'Je pars du Vieux Port. Accepte les animaux de petite taille.', 2); 