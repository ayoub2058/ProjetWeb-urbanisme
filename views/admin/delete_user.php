<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Database connection
        $dsn = 'mysql:host=localhost;dbname=clyptor;charset=utf8mb4';
        $pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $user_id = $_POST['user_id'];

        // Check for foreign key references
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM carpool_rides WHERE driver_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $references = $stmt->fetchColumn();

        if ($references > 0) {
            echo "<script>alert('Cannot delete user. This user is referenced in other records.');</script>";
            header("Location: users.php");
            exit();
        }

        // Proceed with deletion if no references exist
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);

        echo "<script>alert('User deleted successfully.');</script>";
        header("Location: users.php");
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}
?>
