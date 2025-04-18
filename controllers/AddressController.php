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

        $stmt = $pdo->prepare("INSERT INTO user_addresses (user_id, address_line1, address_line2, city, state, postal_code, country, is_default) VALUES (:user_id, :address_line1, :address_line2, :city, :state, :postal_code, :country, 0)");

        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':address_line1' => $_POST['address_line1'],
            ':address_line2' => $_POST['address_line2'],
            ':city' => $_POST['city'],
            ':state' => $_POST['state'],
            ':postal_code' => $_POST['postal_code'],
            ':country' => $_POST['country'],
        ]);

        header('Location: ../views/front/car-rent.php?address_added=1');
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header('Location: ../views/front/car-rent.php');
    exit;
}
