<?php
include_once __DIR__ . "/auth/config.php";

class ReservationC {
    // Lister toutes les reservations
    public function listReservations() {
        $sql = "SELECT * FROM reservation";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Ajouter une nouvelle reservation
    public function addReservation($nom, $prenom, $age, $depuis, $jusqua)
    {
        $sql = "INSERT INTO reservation (nom, prenom, age, depuis, jusqua) 
                VALUES (:nom, :prenom, :age, :depuis, :jusqua)";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'nom'    => $nom,
                'prenom' => $prenom,
                'age'    => $age,
                'depuis' => $depuis,
                'jusqua' => $jusqua
            ]);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }


    // Supprimer une reservation
    public function deleteReservation($id)
    {
        $sql = "DELETE FROM reservation WHERE id = :id";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

   
}
