<?php
namespace GestionReservation; // Déclaration correcte de l'espace de noms

class config {
    public static function getConnexion() {
        $host = 'localhost';
        $dbname = 'gestion_reservation';
        $username = 'root';
        $password = '';

        try {
            $db = new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch (\PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }
}
?>
