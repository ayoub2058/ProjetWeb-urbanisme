<?php
require_once __DIR__ . '/../config/Database.php'; // Corrected path using __DIR__

class CovoiturageModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createRideOffer($data, $imagePath) {
        try {
            $query = "INSERT INTO carpool_rides (driver_id, departure_address_id, destination_address_id, departure_datetime, available_seats, price_per_seat, additional_notes, created_at, status) 
                      VALUES (:driver_id, :departure_address_id, :destination_address_id, :departure_datetime, :available_seats, :price_per_seat, :additional_notes, NOW(), 'scheduled')";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':driver_id', $data['driver_id']);
            $stmt->bindParam(':departure_address_id', $data['departure_address_id']);
            $stmt->bindParam(':destination_address_id', $data['destination_address_id']);
            $stmt->bindParam(':departure_datetime', $data['departure_datetime']);
            $stmt->bindParam(':available_seats', $data['available_seats']);
            $stmt->bindParam(':price_per_seat', $data['price_per_seat']);
            $stmt->bindParam(':additional_notes', $data['additional_notes']);

            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Database error in createRideOffer: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getOrCreateAddress($userId, $address) {
        try {
            // Check if the address already exists
            $query = "SELECT address_id FROM user_addresses WHERE user_id = :user_id AND address_line1 = :address LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':address', $address);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return $result['address_id'];
            }

            // Insert the address if it doesn't exist
            $query = "INSERT INTO user_addresses (user_id, address_line1, city, state, postal_code, country, is_default) 
                      VALUES (:user_id, :address, 'Unknown City', 'Unknown State', '00000', 'Unknown Country', 0)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':address', $address);
            $stmt->execute();

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log('Database error in getOrCreateAddress: ' . $e->getMessage());
            throw new Exception('Failed to validate or create address.');
        }
    }

    public function fetchRideOffers() {
        try {
            $query = "SELECT r.*, 
                             u.username AS user_name, 
                             u.email AS user_email, 
                             (r.driver_id = :current_user_id) AS is_owner,
                             dep.address_line1 AS departure_address,
                             dest.address_line1 AS destination_address
                      FROM carpool_rides r
                      JOIN users u ON r.driver_id = u.user_id
                      JOIN user_addresses dep ON r.departure_address_id = dep.address_id
                      JOIN user_addresses dest ON r.destination_address_id = dest.address_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':current_user_id', 1); // Replace with actual logged-in user ID
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database error in fetchRideOffers: ' . $e->getMessage());
            return [];
        }
    }

    public function deleteRideOffer($id) {
        try {
            $query = "DELETE FROM carpool_rides WHERE ride_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Database error in deleteRideOffer: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function editRideOffer($data) {
        try {
            $query = "UPDATE carpool_rides 
                      SET departure_datetime = :departure_datetime, 
                          available_seats = :available_seats, 
                          price_per_seat = :price_per_seat, 
                          additional_notes = :additional_notes 
                      WHERE ride_id = :ride_id AND driver_id = :driver_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':departure_datetime', $data['departure_datetime']);
            $stmt->bindParam(':available_seats', $data['available_seats']);
            $stmt->bindParam(':price_per_seat', $data['price_per_seat']);
            $stmt->bindParam(':additional_notes', $data['additional_notes']);
            $stmt->bindParam(':ride_id', $data['ride_id']);
            $stmt->bindParam(':driver_id', $data['driver_id']);

            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Database error in editRideOffer: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function reserveSeats($data) {
        try {
            if (empty($data['ride_id'])) {
                throw new Exception('Ride ID cannot be null.');
            }

            error_log('Inserting reservation for Ride ID: ' . $data['ride_id']); // Debugging: Log ride_id before inserting

            $query = "INSERT INTO carpool_bookings (ride_id, passenger_id, seats_booked, total_amount) 
                      VALUES (:ride_id, :passenger_id, :seats_booked, :total_amount)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':ride_id', $data['ride_id']);
            $stmt->bindParam(':passenger_id', $data['passenger_id']);
            $stmt->bindParam(':seats_booked', $data['seats_booked']);
            $stmt->bindParam(':total_amount', $data['total_amount']);

            $stmt->execute();

            // Update available seats in carpool_rides
            $updateQuery = "UPDATE carpool_rides 
                            SET available_seats = available_seats - :seats_booked 
                            WHERE ride_id = :ride_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':seats_booked', $data['seats_booked']);
            $updateStmt->bindParam(':ride_id', $data['ride_id']);
            $updateStmt->execute();

            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Database error in reserveSeats: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
?>
