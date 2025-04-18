<?php

namespace Controllers;

use Models\PackageDelivery;

class PackageDeliveryController
{
    private $model;

    public function __construct(PackageDelivery $model)
    {
        $this->model = $model;
    }

    public function createPost($data)
    {
        if (empty($data['sender_id']) || empty($data['pickup_address_id']) || empty($data['delivery_address_id']) || empty($data['package_description'])) {
            return ['success' => false, 'message' => 'All required fields must be filled.'];
        }

        $result = $this->model->create($data);

        if ($result) {
            return ['success' => true, 'message' => 'Package delivery created successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to create package delivery.'];
        }
    }

    public function getAvailablePackages()
    {
        $query = "SELECT * FROM package_deliveries WHERE status = 'pending' ORDER BY created_at DESC";
        $stmt = $this->model->getDb()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllPackages()
    {
        try {
            $query = "SELECT * FROM package_deliveries ORDER BY created_at DESC";
            $stmt = $this->model->getDb()->prepare($query); // Ensure $this->model->getDb() returns a valid PDO instance
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC); // Use the global PDO class with a leading backslash
        } catch (\Exception $e) { // Use the global Exception class with a leading backslash
            error_log("Error fetching all packages: " . $e->getMessage());
            return [];
        }
    }

    public function editPost($data)
    {
        if (empty($data['delivery_id'])) {
            return ['success' => false, 'message' => 'Delivery ID is required.'];
        }

        try {
            $query = "UPDATE package_deliveries 
                      SET package_description = :package_description,
                          package_weight = :package_weight,
                          package_dimensions = :package_dimensions,
                          estimated_value = :estimated_value,
                          delivery_deadline = :delivery_deadline,
                          proposed_price = :proposed_price
                      WHERE delivery_id = :delivery_id AND sender_id = :sender_id";

            $stmt = $this->model->getDb()->prepare($query);
            $result = $stmt->execute([
                ':package_description' => $data['package_description'],
                ':package_weight' => $data['package_weight'],
                ':package_dimensions' => $data['package_dimensions'],
                ':estimated_value' => $data['estimated_value'],
                ':delivery_deadline' => $data['delivery_deadline'],
                ':proposed_price' => $data['proposed_price'],
                ':delivery_id' => $data['delivery_id'],
                ':sender_id' => $_SESSION['user_id'], // Ensure only the owner can edit
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Package delivery updated successfully.'];
            } else {
                return ['success' => false, 'message' => 'Failed to update package delivery.'];
            }
        } catch (Exception $e) {
            error_log("Error editing package delivery: " . $e->getMessage());
            return ['success' => false, 'message' => 'An unexpected error occurred. Please try again later.'];
        }
    }

    public function deletePost($deliveryId)
    {
        if (empty($deliveryId)) {
            return ['success' => false, 'message' => 'Delivery ID is required.'];
        }

        try {
            $query = "DELETE FROM package_deliveries WHERE delivery_id = :delivery_id AND sender_id = :sender_id";
            $stmt = $this->model->getDb()->prepare($query);
            $result = $stmt->execute([
                ':delivery_id' => $deliveryId,
                ':sender_id' => $_SESSION['user_id'], // Ensure only the owner can delete their post
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Package delivery deleted successfully.'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete package delivery.'];
            }
        } catch (Exception $e) {
            error_log("Error deleting package delivery: " . $e->getMessage());
            return ['success' => false, 'message' => 'An unexpected error occurred. Please try again later.'];
        }
    }
}
