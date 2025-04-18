<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/front/login.php');
            exit;
        }

        $pdo = new PDO('mysql:host=127.0.0.1;dbname=clyptor', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO vehicles (owner_id, make, model, year, color, license_plate, vehicle_type, seat_capacity, fuel_type, is_available) VALUES (:owner_id, :make, :model, :year, :color, :license_plate, :vehicle_type, :seat_capacity, :fuel_type, 1)");

        $stmt->execute([
            ':owner_id' => $_SESSION['user_id'], // Use the logged-in user's ID
            ':make' => $_POST['make'],
            ':model' => $_POST['model'],
            ':year' => $_POST['year'],
            ':color' => $_POST['color'],
            ':license_plate' => $_POST['license_plate'],
            ':vehicle_type' => $_POST['vehicle_type'],
            ':seat_capacity' => $_POST['seat_capacity'],
            ':fuel_type' => $_POST['fuel_type'],
        ]);

        header('Location: ../views/front/car-rent.php?vehicle_added=1');
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header('Location: ../views/front/car-rent.php');
    exit;
}
