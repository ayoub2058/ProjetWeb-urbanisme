<?php

class HomeRentalsModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO home_rentals (owner_id, address_id, title, description, property_type, bedrooms, bathrooms, max_guests, daily_rate, available_from, available_to, minimum_stay, amenities, main_photo_url, status) 
                VALUES (:owner_id, :address_id, :title, :description, :property_type, :bedrooms, :bathrooms, :max_guests, :daily_rate, :available_from, :available_to, :minimum_stay, :amenities, :main_photo_url, 'available')";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function fetchAll() {
        $sql = "SELECT * FROM home_rentals WHERE status = 'available'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($rentalId) {
        $sql = "DELETE FROM home_rentals WHERE rental_id = :rental_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':rental_id', $rentalId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function update($data) {
        $sql = "UPDATE home_rentals 
                SET title = :title, description = :description, property_type = :property_type, 
                    bedrooms = :bedrooms, bathrooms = :bathrooms, max_guests = :max_guests, 
                    daily_rate = :daily_rate, available_from = :available_from, available_to = :available_to, 
                    minimum_stay = :minimum_stay, amenities = :amenities, main_photo_url = :main_photo_url 
                WHERE rental_id = :rental_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
}
?>
