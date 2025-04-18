<?php
require_once __DIR__ . '/../models/CovoiturageModel.php';

class CovoiturageController {
    private $model;

    public function __construct() {
        $this->model = new CovoiturageModel();
    }

    public function createRideOffer($postData, $fileData) {
        try {
            $driverId = 1; // Replace with actual logged-in user ID

            // Validate or create departure and destination addresses
            $departureAddressId = $this->model->getOrCreateAddress($driverId, $postData['departure']);
            $destinationAddressId = $this->model->getOrCreateAddress($driverId, $postData['destination']);

            $departureDatetime = $postData['date'] . ' ' . $postData['time'];
            $availableSeats = $postData['seats'];
            $pricePerSeat = $postData['price'];
            $additionalNotes = $postData['description'];

            $imagePath = null;
            if (isset($fileData['image']) && $fileData['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = '../../uploads/' . basename($fileData['image']['name']);
                if (!move_uploaded_file($fileData['image']['tmp_name'], $imagePath)) {
                    throw new Exception('Failed to upload image.');
                }
            }

            $data = [
                'driver_id' => $driverId,
                'departure_address_id' => $departureAddressId,
                'destination_address_id' => $destinationAddressId,
                'departure_datetime' => $departureDatetime,
                'available_seats' => $availableSeats,
                'price_per_seat' => $pricePerSeat,
                'additional_notes' => $additionalNotes
            ];

            $result = $this->model->createRideOffer($data, $imagePath);
            echo json_encode($result);
        } catch (Exception $e) {
            error_log('Error in createRideOffer: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function fetchRideOffers() {
        echo json_encode($this->model->fetchRideOffers());
    }

    public function deleteRideOffer($id) {
        echo json_encode($this->model->deleteRideOffer($id));
    }

    public function editRideOffer($postData) {
        try {
            $result = $this->model->editRideOffer($postData);
            echo json_encode($result);
        } catch (Exception $e) {
            error_log('Error in editRideOffer: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function reserveSeats($postData) {
        try {
            if (empty($postData['ride_id'])) {
                throw new Exception('Ride ID is required.');
            }

            error_log('Ride ID received: ' . $postData['ride_id']); // Debugging: Log ride_id to ensure it's being received

            $result = $this->model->reserveSeats($postData);
            echo json_encode($result);
        } catch (Exception $e) {
            error_log('Error in reserveSeats: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CovoiturageController();
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'edit') {
            $controller->editRideOffer($_POST);
        } elseif ($_GET['action'] === 'reserve') {
            $controller->reserveSeats($_POST);
        } else {
            $controller->createRideOffer($_POST, $_FILES);
        }
    } else {
        $controller->createRideOffer($_POST, $_FILES);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $controller = new CovoiturageController();
    $controller->fetchRideOffers();
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $controller = new CovoiturageController();
    $controller->deleteRideOffer($_GET['id']);
}
?>
