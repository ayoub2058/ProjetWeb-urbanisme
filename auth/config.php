<?php
// Paramètres de connexion
$host = 'localhost';        // ou 127.0.0.1
$dbname = 'post';           // nom de la base de données
$username = 'root';         // nom d'utilisateur MySQL (par défaut : root)
$password = '';             // mot de passe (souvent vide en local)

// Connexion à la base de données avec PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Activer les erreurs PDO en mode exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur, afficher un message
    die("Erreur de connexion : " . $e->getMessage());
}

class config {
    public static function getConnexion()
    {
        global $pdo;
        return $pdo;
    }
}
?>
