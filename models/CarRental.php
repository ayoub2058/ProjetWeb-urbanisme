<?php

class CarRental {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all available car rentals
    public function getAvailableRentals() {
        $stmt = $this->pdo->prepare("SELECT * FROM car_rentals WHERE status = 'available'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new car rental
    public function createRental($data) {
        $stmt = $this->pdo->prepare("INSERT INTO car_rentals (vehicle_id, owner_id, daily_rate, minimum_rental_days, available_from, available_to, pickup_location_id, description, insurance_included, status) VALUES (:vehicle_id, :owner_id, :daily_rate, :minimum_rental_days, :available_from, :available_to, :pickup_location_id, :description, :insurance_included, 'available')");
        return $stmt->execute($data);
    }

    public function updateRental($data) {
        $stmt = $this->pdo->prepare("UPDATE car_rentals SET daily_rate = :daily_rate, description = :description WHERE rental_id = :rental_id AND owner_id = :owner_id");
        return $stmt->execute([
            ':daily_rate' => $data['daily_rate'],
            ':description' => $data['description'],
            ':rental_id' => $data['rental_id'],
            ':owner_id' => $data['owner_id']
        ]);
    }

    public function deleteRental($rental_id, $owner_id) {
        $stmt = $this->pdo->prepare("DELETE FROM car_rentals WHERE rental_id = :rental_id AND owner_id = :owner_id");
        return $stmt->execute([
            ':rental_id' => $rental_id,
            ':owner_id' => $owner_id
        ]);
    }
}
