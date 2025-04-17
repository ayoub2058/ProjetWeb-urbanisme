<?php
class config {
    public static function getConnexion() {
        $host = 'localhost';
        $dbname = 'gestion_reservation';
        $username = 'root';
        $password = '';

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }
}
?>
