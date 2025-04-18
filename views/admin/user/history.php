<?php
require_once __DIR__ . '/../../../config/database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../front/login.php");
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
$sql = "SELECT ride_id, departure_datetime, available_seats, price_per_seat, additional_notes, created_at 
        FROM carpool_rides 
        WHERE driver_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <aside>
        <div class="toggle">
            <div class="logo">
                <img src="images/logo.png">
                <h2>Cly<span class="danger">Ptor</span></h2>
            </div>
            <div class="close" id="close-btn">
                <span class="material-icons-sharp">
                    close
                </span>
            </div>
        </div>
      <div class="sidebar">
        <a href="user-info.php">
          <span class="fas fa-user"></span>
          <h3>User Information</h3>
        </a>
        <a href="history.php" class="active">
          <span class="fas fa-history"></span>
          <h3>History</h3>
        </a>
        <a href="chat.php">
          <span class="fas fa-comments"></span>
          <h3>Chat</h3>
        </a>
        <a href="#" id="logout-button">
          <span class="fas fa-sign-out-alt"></span>
          <h3>Logout</h3>
        </a>
      </div>
    </aside>
    <main>
      <h1>History</h1>
      <div id="posts-container">
        <?php if (empty($rides)): ?>
          <p>No ride offers found.</p>
        <?php else: ?>
          <?php foreach ($rides as $ride): ?>
            <div class="post-card">
              <h3>Ride ID: <?php echo htmlspecialchars($ride['ride_id']); ?></h3>
              <p><strong>Departure:</strong> <?php echo htmlspecialchars($ride['departure_datetime']); ?></p>
              <p><strong>Seats Available:</strong> <?php echo htmlspecialchars($ride['available_seats']); ?></p>
              <p><strong>Price per Seat:</strong> $<?php echo htmlspecialchars($ride['price_per_seat']); ?></p>
              <p><strong>Notes:</strong> <?php echo htmlspecialchars($ride['additional_notes'] ?? 'N/A'); ?></p>
              <div class="post-actions">
                <a href="edit-ride.php?id=<?php echo $ride['ride_id']; ?>" class="edit-btn">Edit</a>
                <a href="delete-ride.php?id=<?php echo $ride['ride_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this ride?');">Delete</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </main>
  </div>
  <script>
    document.getElementById("logout-button").addEventListener("click", () => {
      localStorage.removeItem("isLoggedIn");
      localStorage.removeItem("username");
      window.location.href = "../index.php";
    });
  </script>
</body>
</html>