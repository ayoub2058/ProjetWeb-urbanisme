<?php
require_once __DIR__ . '/../../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Fetch user information
$user_id = 1; // Replace with dynamic user ID if needed
$sql = "SELECT username, email, first_name, last_name, phone_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Information</title>
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
        <a href="user-info.php" class="active">
          <span class="fas fa-user"></span>
          <h3>User Information</h3>
        </a>
        <a href="history.php">
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
      <h1>User Information</h1>
      <div class="card user-info-card">
        <h3><i class="fas fa-user-circle"></i> Username: <span id="username-display"><?php echo htmlspecialchars($user['username']); ?></span></h3>
        <h3><i class="fas fa-envelope"></i> Email: <span id="email-display"><?php echo htmlspecialchars($user['email']); ?></span></h3>
        <h3><i class="fas fa-user"></i> Full Name: <span id="fullname-display"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span></h3>
        <h3><i class="fas fa-phone"></i> Phone: <span id="phone-display"><?php echo htmlspecialchars($user['phone_number']); ?></span></h3>
        <button class="btn btn-primary">Edit Information</button>
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