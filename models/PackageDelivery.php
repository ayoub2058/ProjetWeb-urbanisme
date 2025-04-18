<?php

namespace Models;

use PDO;

class PackageDelivery
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create($data)
    {
        $query = "INSERT INTO package_deliveries (sender_id, pickup_address_id, delivery_address_id, package_description, package_weight, package_dimensions, estimated_value, delivery_deadline, proposed_price, status) 
                  VALUES (:sender_id, :pickup_address_id, :delivery_address_id, :package_description, :package_weight, :package_dimensions, :estimated_value, :delivery_deadline, :proposed_price, 'pending')";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':sender_id' => $data['sender_id'],
            ':pickup_address_id' => $data['pickup_address_id'],
            ':delivery_address_id' => $data['delivery_address_id'],
            ':package_description' => $data['package_description'],
            ':package_weight' => $data['package_weight'],
            ':package_dimensions' => $data['package_dimensions'],
            ':estimated_value' => $data['estimated_value'],
            ':delivery_deadline' => $data['delivery_deadline'],
            ':proposed_price' => $data['proposed_price']
        ]);
    }

    public function getDb()
    {
        return $this->db;
    }
}
