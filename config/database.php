<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            // D'abord, se connecter sans spécifier la base de données
            $this->conn = new PDO(
                "mysql:host=localhost",
                "root",
                "",
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Créer la base de données si elle n'existe pas
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS post_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Sélectionner la base de données
            $this->conn->exec("USE post_db");

            // Créer la table si elle n'existe pas
            $this->conn->exec("CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                titre VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                image VARCHAR(255),
                mail VARCHAR(255) NOT NULL,
                status ENUM('disponible', 'reserve') DEFAULT 'disponible',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
} 