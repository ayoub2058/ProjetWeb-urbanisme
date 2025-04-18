<?php
require_once __DIR__ . '/../config/Database.php';

class CarRentalController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        $stmt = $this->pdo->query("SELECT * FROM car_rentals WHERE status = 'available'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO car_rentals (vehicle_id, owner_id, daily_rate, minimum_rental_days, available_from, available_to, pickup_location_id, description, insurance_included, status) VALUES (:vehicle_id, :owner_id, :daily_rate, :minimum_rental_days, :available_from, :available_to, :pickup_location_id, :description, :insurance_included, 'available')");
        $stmt->execute([
            ':vehicle_id' => $data['vehicle_id'],
            ':owner_id' => $data['owner_id'],
            ':daily_rate' => $data['daily_rate'],
            ':minimum_rental_days' => $data['minimum_rental_days'],
            ':available_from' => $data['available_from'],
            ':available_to' => $data['available_to'],
            ':pickup_location_id' => $data['pickup_location_id'],
            ':description' => $data['description'],
            ':insurance_included' => isset($data['insurance_included']) ? 1 : 0,
        ]);
    }

    public function update($data) {
        $stmt = $this->pdo->prepare("UPDATE car_rentals SET daily_rate = :daily_rate, description = :description WHERE rental_id = :rental_id AND owner_id = :owner_id");
        $stmt->execute([
            ':daily_rate' => $data['daily_rate'],
            ':description' => $data['description'],
            ':rental_id' => $data['rental_id'],
            ':owner_id' => $data['owner_id'],
        ]);
    }

    public function delete($rental_id, $owner_id) {
        $stmt = $this->pdo->prepare("DELETE FROM car_rentals WHERE rental_id = :rental_id AND owner_id = :owner_id");
        $stmt->execute([
            ':rental_id' => $rental_id,
            ':owner_id' => $owner_id,
        ]);
    }
}

// Ensure $pdo is initialized before handling POST requests
$pdo = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CarRentalController($pdo);

    if (isset($_POST['delete_post'])) {
        $controller->delete($_POST['delete_id'], $_SESSION['user_id']);
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?deleted=1');
        exit;
    }

    if (isset($_POST['edit_post'])) {
        $controller->update([
            'daily_rate' => $_POST['daily_rate'],
            'description' => $_POST['description'],
            'rental_id' => $_POST['edit_id'],
            'owner_id' => $_SESSION['user_id']
        ]);
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?edited=1');
        exit;
    }
}
?>
