<?php
include_once __DIR__ . "/auth/config.php";

class ReservationC {
<<<<<<< Updated upstream
    // Lister toutes les reservations
=======

    // Lister toutes les réservations
>>>>>>> Stashed changes
    public function listReservations() {
        $sql = "SELECT * FROM reservation";
        $db = config::getConnexion();
        try {
<<<<<<< Updated upstream
            return $db->query($sql);
=======
            return $db->query($sql)->fetchAll();
>>>>>>> Stashed changes
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

<<<<<<< Updated upstream
    // Ajouter une nouvelle reservation
    public function addReservation($nom, $prenom, $age, $depuis, $jusqua)
    {
        $sql = "INSERT INTO reservation (nom, prenom, age, depuis, jusqua) 
                VALUES (:nom, :prenom, :age, :depuis, :jusqua)";
=======
    // Ajouter une nouvelle réservation
    public function addReservation($nom, $prenom, $age, $duree) {
        // Validation côté serveur
        if (!is_numeric($age) || $age < 18 || $age > 120) {
            die("Âge invalide. Il doit être entre 18 et 120.");
        }
        if (!is_numeric($duree) || $duree <= 0) {
            die("Durée invalide. Elle doit être supérieure à 0.");
        }

        $sql = "INSERT INTO reservation (nom, prenom, age, duree) 
                VALUES (:nom, :prenom, :age, :duree)";
>>>>>>> Stashed changes
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'nom'    => $nom,
                'prenom' => $prenom,
                'age'    => $age,
<<<<<<< Updated upstream
                'depuis' => $depuis,
                'jusqua' => $jusqua
=======
                'duree'  => $duree
>>>>>>> Stashed changes
            ]);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

<<<<<<< Updated upstream

    // Supprimer une reservation
    public function deleteReservation($id)
    {
=======
    // Mettre à jour une réservation
    public function updateReservation($id, $nom, $prenom, $age, $duree) {
        if (!is_numeric($age) || $age < 18 || $age > 120) {
            die("Âge invalide.");
        }
        if (!is_numeric($duree) || $duree <= 0) {
            die("Durée invalide.");
        }

        $sql = "UPDATE reservation 
                SET nom = :nom, prenom = :prenom, age = :age, duree = :duree 
                WHERE id = :id";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id'     => $id,
                'nom'    => $nom,
                'prenom' => $prenom,
                'age'    => $age,
                'duree'  => $duree
            ]);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Supprimer une réservation
    public function deleteReservation($id) {
>>>>>>> Stashed changes
        $sql = "DELETE FROM reservation WHERE id = :id";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
<<<<<<< Updated upstream

   
}
=======
// Récupérer une réservation par son ID
public function getReservation($id) {
    $sql = "SELECT * FROM reservation WHERE id = :id";
    $db = config::getConnexion();
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();  // Retourne la réservation trouvée sous forme de tableau associatif
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}



    
}
?>
>>>>>>> Stashed changes
