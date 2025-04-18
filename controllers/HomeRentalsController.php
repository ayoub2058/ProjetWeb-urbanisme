<?php
require_once __DIR__ . '/../models/HomeRentalsModel.php';

class HomeRentalsController {
    private $model;

    public function __construct($pdo) {
        $this->model = new HomeRentalsModel($pdo);
    }

    public function createPropertyListing($postData, $files) {
        // Validate and sanitize inputs
        $data = [
            'owner_id' => $postData['owner_id'] ?? 1, // Default to 1 if not provided
            'address_id' => $postData['address_id'] ?? 1, // Default to 1 if not provided
            'title' => $postData['title'] ?? '',
            'description' => $postData['description'] ?? '',
            'property_type' => $postData['property_type'] ?? 'apartment',
            'bedrooms' => $postData['bedrooms'] ?? 0,
            'bathrooms' => $postData['bathrooms'] ?? 0,
            'max_guests' => $postData['max_guests'] ?? 1,
            'daily_rate' => $postData['price'] ?? 0.0,
            'available_from' => $postData['available_from'] ?? null,
            'available_to' => $postData['available_to'] ?? null,
            'minimum_stay' => $postData['minimum_stay'] ?? 1,
            'amenities' => isset($postData['amenities']) ? implode(',', $postData['amenities']) : '',
            'main_photo_url' => $this->uploadImage($files['images'] ?? null),
        ];

        // Ensure required fields are not empty
        if (empty($data['title']) || empty($data['description']) || empty($data['daily_rate'])) {
            return false;
        }

        // Call the model to insert the data
        return $this->model->create($data);
    }

    public function fetchPropertyListings() {
        return $this->model->fetchAll();
    }

    public function deletePropertyListing($rentalId) {
        return $this->model->delete($rentalId);
    }

    public function updatePropertyListing($postData, $files) {
        $data = [
            'rental_id' => $postData['rental_id'],
            'title' => $postData['title'],
            'description' => $postData['description'],
            'property_type' => $postData['property_type'],
            'bedrooms' => $postData['bedrooms'],
            'bathrooms' => $postData['bathrooms'],
            'max_guests' => $postData['max_guests'],
            'daily_rate' => $postData['price'],
            'available_from' => $postData['available_from'],
            'available_to' => $postData['available_to'],
            'minimum_stay' => $postData['minimum_stay'],
            'amenities' => isset($postData['amenities']) ? implode(',', $postData['amenities']) : '',
            'main_photo_url' => $this->uploadImage($files['images'] ?? null, $postData['existing_photo']),
        ];

        return $this->model->update($data);
    }

    private function uploadImage($file, $existingPhoto = null) {
        $targetDir = __DIR__ . "/../uploads/"; // Use absolute path for the uploads directory

        // Ensure the uploads directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Create the directory with write permissions
        }

        if ($file && isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'][0])) {
            $fileName = time() . "_" . basename($file["name"][0]);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($file["tmp_name"][0], $targetFilePath)) {
                return $fileName;
            } else {
                error_log("Failed to move uploaded file to $targetFilePath");
            }
        }

        return $existingPhoto; // Return the existing photo if no new file is uploaded
    }
}
?>
