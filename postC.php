<?php
// Inclure correctement le fichier de configuration avec un chemin absolu si nécessaire
require_once __DIR__ . '/auth/config.php';  // Chemin absolu

// Importer la classe config de l'espace de noms GestionReservation
use GestionReservation\config;

class PostC {

    // Récupérer tous les posts avec un critère de tri
    public function listPosts($sortBy = 'titre') {
        // Utilisation de la méthode getConnexion() pour obtenir la connexion
        $db = config::getConnexion();
        
        // Vérifier si le critère de tri est valide
        $allowedSortColumns = ['titre', 'date'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'titre'; // Par défaut trier par titre
        }

        // Préparer la requête SQL avec tri
        $query = $db->prepare("SELECT * FROM post ORDER BY $sortBy ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>
