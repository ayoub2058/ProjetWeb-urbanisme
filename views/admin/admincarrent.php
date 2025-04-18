<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once '../../controllers/CarRentalController.php';
require_once '../../config/Database.php';

// Initialize the database connection
$pdo = (new Database())->getConnection();

// Initialize the controller
$controller = new CarRentalController($pdo);

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $controller->delete($_POST['delete_id'], $_SESSION['user_id']);
    header('Location: admincarrent.php?deleted=1');
    exit;
}

// Handle edit action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {
    $controller->update([
        'daily_rate' => $_POST['daily_rate'],
        'description' => $_POST['description'],
        'rental_id' => $_POST['edit_id'],
        'owner_id' => $_SESSION['user_id']
    ]);
    header('Location: admincarrent.php?edited=1');
    exit;
}

// Fetch all rentals
$rentals = $controller->index();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Car Rentals</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar Section -->
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
                <a href="#">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="users.php">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>Users</h3>
                </a>
                <a href="">
                    <span class="material-icons-sharp">
                        receipt_long
                    </span>
                    <h3>History</h3>
                </a>
                <div class="submenu">
                    <a href="admincarrent.php" class="active">Car Rent</a>
                    <a href="history2.php">Covoiturage</a>
                    <a href="adminrent.php">Home Rent</a>
                    <a href="adminpackage.php">Deliver Package</a>
                </div>
                <a href="#">
                    <span class="material-icons-sharp">
                        insights
                    </span>
                    <h3>Analytics</h3>
                </a>
                <a href="ticket.php">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Tickets</h3>
                    <span class="message-count">27</span>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        inventory
                    </span>
                    <h3>Trajet</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        add
                    </span>
                    <h3>New Login</h3>
                </a>
                <a href="logout.html">
                    <span class="material-icons-sharp">
                        logout
                    </span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Manage Car Rentals</h1>
            <?php if (isset($_GET['deleted'])): ?>
                <p style="color: green;">Post deleted successfully.</p>
            <?php endif; ?>
            <?php if (isset($_GET['edited'])): ?>
                <p style="color: green;">Post updated successfully.</p>
            <?php endif; ?>
            <div class="recent-orders">
                <h2>Available Vehicles</h2>
                <div class="posts-container">
                    <?php if (!empty($rentals)): ?>
                        <?php foreach ($rentals as $rental): ?>
                            <div class="post-card">
                                <div class="post-header">
                                    <h3><?= htmlspecialchars($rental['description']) ?></h3>
                                    <span class="post-price">$<?= htmlspecialchars($rental['daily_rate']) ?>/day</span>
                                </div>
                                <div class="post-image">
                                    <img src="../uploads/<?= htmlspecialchars($rental['vehicle_id'] ?? 'placeholder.jpg') ?>" alt="Vehicle Image">
                                </div>
                                <div class="post-body">
                                    <p><strong>Available From:</strong> <?= htmlspecialchars($rental['available_from']) ?></p>
                                    <p><strong>Available To:</strong> <?= htmlspecialchars($rental['available_to']) ?></p>
                                    <p><strong>Pickup Location:</strong> <?= htmlspecialchars($rental['pickup_location_id']) ?></p>
                                    <p><strong>Minimum Rental Days:</strong> <?= htmlspecialchars($rental['minimum_rental_days']) ?></p>
                                    <p><strong>Insurance Included:</strong> <?= $rental['insurance_included'] ? 'Yes' : 'No' ?></p>
                                </div>
                                <div class="post-footer">
                                    <span class="post-author">Posted by Owner ID: <?= htmlspecialchars($rental['owner_id']) ?></span>
                                    <span class="post-date"><?= htmlspecialchars(date('F j, Y', strtotime($rental['created_at']))) ?></span>
                                </div>
                                <div class="post-actions">
                                    <form method="POST" action="admincarrent.php">
                                        <input type="hidden" name="edit_id" value="<?= htmlspecialchars($rental['rental_id']) ?>">
                                        <input type="hidden" name="edit_post" value="1">
                                        <label for="daily_rate">Daily Rate ($)</label>
                                        <input type="number" name="daily_rate" value="<?= htmlspecialchars($rental['daily_rate']) ?>" required>
                                        <label for="description">Description</label>
                                        <textarea name="description" required><?= htmlspecialchars($rental['description']) ?></textarea>
                                        <button type="submit" class="action-btn edit-btn">Edit</button>
                                    </form>
                                    <form method="POST" action="admincarrent.php">
                                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($rental['rental_id']) ?>">
                                        <input type="hidden" name="delete_post" value="1">
                                        <button type="submit" class="action-btn delete-btn">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No vehicles available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">
                        menu
                    </span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">
                        light_mode
                    </span>
                    <span class="material-icons-sharp">
                        dark_mode
                    </span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p>Hey, <b>Admin</b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <img src="images/profile-1.jpg">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="orders.js"></script>
    <script src="index.js"></script>
</body>
</html>
