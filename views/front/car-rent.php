<?php
// Start the session only if it is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/front/login.php');
    exit;
}

// Include the required files
require_once '../../controllers/CarRentalController.php';
require_once '../../config/Database.php';

// Initialize the database connection
$pdo = (new Database())->getConnection();

// Initialize the controller
$controller = new CarRentalController($pdo);

// Fetch available rentals
$rentals = $controller->index();

// Fetch available vehicles
$vehicleStmt = $pdo->query("SELECT vehicle_id, make, model, year FROM vehicles WHERE is_available = 1");
$vehicles = $vehicleStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available addresses for the logged-in user
$addressStmt = $pdo->prepare("SELECT address_id, address_line1, city, state, postal_code FROM user_addresses WHERE user_id = :user_id");
$addressStmt->execute([':user_id' => $_SESSION['user_id']]);
$addresses = $addressStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete action
if (isset($_GET['delete_id'])) {
    $deleteStmt = $pdo->prepare("DELETE FROM car_rentals WHERE rental_id = :rental_id AND owner_id = :owner_id");
    $deleteStmt->execute([
        ':rental_id' => $_GET['delete_id'],
        ':owner_id' => $_SESSION['user_id']
    ]);
    header('Location: car-rent.php?deleted=1');
    exit;
}

// Handle edit action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $editStmt = $pdo->prepare("UPDATE car_rentals SET daily_rate = :daily_rate, description = :description WHERE rental_id = :rental_id AND owner_id = :owner_id");
    $editStmt->execute([
        ':daily_rate' => $_POST['daily_rate'],
        ':description' => $_POST['description'],
        ':rental_id' => $_POST['edit_id'],
        ':owner_id' => $_SESSION['user_id']
    ]);
    header('Location: car-rent.php?edited=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clyptor - Car Rent</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/hf.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header class="header">
    <div class="logo-container">
        <a href="index.php" class="logo">
            <!-- <img src="assets/images/logo.png" alt="Clyptor Logo"> -->
            <span class="logo-text">Clyptor</span>
        </a>
        <div class="logo-3d"></div>
    </div>
    
    <nav class="main-nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="covoiturage.php">Carpooling</a></li>
            <li><a href="home-rent.php">Home Rent</a></li>
            <li><a href="car-rent.php">Car Rent</a></li>
            <li><a href="deliver-package.php">Deliver Package</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>
    
    <div class="auth-buttons">
        <a href="login.php" class="btn btn-outline">Login</a>
        <a href="register.php" class="btn btn-primary">Register</a>
    </div>
    
    <button class="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
</header>

<main class="service-main">
    <?php if (isset($_GET['deleted'])): ?>
        <p style="color: green;">Post deleted successfully.</p>
    <?php endif; ?>
    <?php if (isset($_GET['edited'])): ?>
        <p style="color: green;">Post updated successfully.</p>
    <?php endif; ?>
    <section class="service-hero">
        <div class="hero-content">
            <h1>Car Rental</h1>
            <p>Rent cars by the hour, day, or week from local owners.</p>
            <button id="create-post-btn" class="cta-button">List Your Vehicle</button>
        </div>
        <div class="hero-image">
            <spline-viewer url="https://prod.spline.design/W9odOGD6ccShUyRp/scene.splinecode"></spline-viewer>
            <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.js"></script>
        </div>
    </section>

    <!-- Modal for creating a post -->
    <div id="post-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span id="close-modal" class="close">&times;</span>
            <h2>List Your Vehicle</h2>
            <form id="create-post-form" action="/ghodwa/controllers/CarRentalController.php" method="POST">
                <div class="form-group">
                    <label for="vehicle_id">Select Vehicle</label>
                    <select name="vehicle_id" id="vehicle_id" required>
                        <option value="">-- Select a Vehicle --</option>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?php echo $vehicle['vehicle_id']; ?>">
                                <?php echo htmlspecialchars($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small id="vehicle-error" style="color: red; display: none;">Please select a vehicle.</small>
                </div>
                <script>
                    // Client-side validation for vehicle selection
                    const vehicleSelect = document.getElementById('vehicle_id');
                    const vehicleError = document.getElementById('vehicle-error');
                    const createPostForm = document.getElementById('create-post-form');

                    createPostForm.addEventListener('submit', (event) => {
                        if (!vehicleSelect.value) {
                            event.preventDefault();
                            vehicleError.style.display = 'block';
                        } else {
                            vehicleError.style.display = 'none';
                        }
                    });
                </script>
                <input type="hidden" name="owner_id" value="<?php echo $_SESSION['user_id']; ?>">
                <div class="form-group">
                    <label for="daily_rate">Daily Rate ($)</label>
                    <input type="number" name="daily_rate" id="daily_rate" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="minimum_rental_days">Minimum Rental Days</label>
                    <input type="number" name="minimum_rental_days" id="minimum_rental_days" min="1" required>
                </div>
                <div class="form-group">
                    <label for="available_from">Available From</label>
                    <input type="date" name="available_from" id="available_from" required>
                </div>
                <div class="form-group">
                    <label for="available_to">Available To</label>
                    <input type="date" name="available_to" id="available_to" required>
                </div>
                <div class="form-group">
                    <label for="pickup_location_id">Pickup Location</label>
                    <?php if (!empty($addresses)): ?>
                        <select name="pickup_location_id" id="pickup_location_id" required>
                            <option value="">-- Select a Pickup Location --</option>
                            <?php foreach ($addresses as $address): ?>
                                <option value="<?php echo $address['address_id']; ?>">
                                    <?php echo htmlspecialchars($address['address_line1'] . ', ' . $address['city'] . ', ' . $address['state'] . ' ' . $address['postal_code']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <p>No addresses found. Please add a new address below.</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="insurance_included">Insurance Included</label>
                    <input type="checkbox" name="insurance_included" id="insurance_included" value="1">
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Create</button>
                    <button type="button" id="cancel-modal" class="cancel-btn">Cancel</button>
                </div>
            </form>

            <hr>

            <h2>Add a New Vehicle</h2>
            <form id="add-vehicle-form" action="/ghodwa/controllers/VehicleController.php" method="POST">
                <div class="form-group">
                    <label for="make">Make</label>
                    <input type="text" name="make" id="make" required>
                </div>
                <div class="form-group">
                    <label for="model">Model</label>
                    <input type="text" name="model" id="model" required>
                </div>
                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="number" name="year" id="year" min="1980" max="<?php echo date('Y'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="color">Color</label>
                    <input type="text" name="color" id="color" required>
                </div>
                <div class="form-group">
                    <label for="license_plate">License Plate</label>
                    <input type="text" name="license_plate" id="license_plate" required>
                </div>
                <div class="form-group">
                    <label for="vehicle_type">Vehicle Type</label>
                    <select name="vehicle_type" id="vehicle_type" required>
                        <option value="sedan">Sedan</option>
                        <option value="suv">SUV</option>
                        <option value="truck">Truck</option>
                        <option value="van">Van</option>
                        <option value="motorcycle">Motorcycle</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="seat_capacity">Seat Capacity</label>
                    <input type="number" name="seat_capacity" id="seat_capacity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="fuel_type">Fuel Type</label>
                    <select name="fuel_type" id="fuel_type" required>
                        <option value="gasoline">Gasoline</option>
                        <option value="diesel">Diesel</option>
                        <option value="electric">Electric</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Add Vehicle</button>
                </div>
            </form>

            <hr>

            <h2>Add a New Address</h2>
            <form id="add-address-form" action="/ghodwa/controllers/AddressController.php" method="POST">
                <div class="form-group">
                    <label for="address_line1">Address Line 1</label>
                    <input type="text" name="address_line1" id="address_line1" required>
                </div>
                <div class="form-group">
                    <label for="address_line2">Address Line 2</label>
                    <input type="text" name="address_line2">
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" required>
                </div>
                <div class="form-group">
                    <label for="state">State</label>

                    <input type="text" name="state" id="state" required>
                </div>
                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" required>
                </div>
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" name="country" id="country" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Add Address</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const createPostBtn = document.getElementById('create-post-btn');
            const postModal = document.getElementById('post-modal');
            const closeModal = document.getElementById('close-modal');
            const cancelModal = document.getElementById('cancel-modal');

            // Show modal on button click
            createPostBtn.addEventListener('click', function () {
                postModal.style.display = 'block';
            });

            // Close modal on close button click
            closeModal.addEventListener('click', function () {
                postModal.style.display = 'none';
            });

            // Close modal on cancel button click
            cancelModal.addEventListener('click', function () {
                postModal.style.display = 'none';
            });

            // Close modal when clicking outside the modal content
            window.addEventListener('click', function (event) {
                if (event.target === postModal) {
                    postModal.style.display = 'none';
                }
            });

            // Handle delete action via AJAX
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Post deleted successfully.');
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Failed to delete post.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });

            // Handle edit action via AJAX
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Post updated successfully.');
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Failed to update post.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>

    <section class="posts-section">
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
                            <form method="POST" action="car-rent.php">
                                <input type="hidden" name="edit_id" value="<?= htmlspecialchars($rental['rental_id']) ?>">
                                <input type="hidden" name="edit_post" value="1">
                                <label for="daily_rate">Daily Rate ($)</label>
                                <input type="number" name="daily_rate" value="<?= htmlspecialchars($rental['daily_rate']) ?>" required>
                                <label for="description">Description</label>
                                <textarea name="description" required><?= htmlspecialchars($rental['description']) ?></textarea>
                                <button type="submit" class="action-btn edit-btn">Edit</button>
                            </form>
                            <form method="POST" action="car-rent.php">
                                <input type="hidden" name="delete_id" value="<?= htmlspecialchars($rental['rental_id']) ?>">
                                <input type="hidden" name="delete_post" value="1">
                                <button type="submit" class="action-btn delete-btn">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No vehicles available. List your vehicle now!</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section about">
            <div class="logo-container">
                <a href="index.php" class="logo"></a>
                    <!-- <img src="assets/images/logo.png" alt="Clyptor Logo"> -->
                    <span class="logo-text">Clyptor</span>
                </a>
            </div>
            <p>Clyptor provides innovative solutions for carpooling, home rentals, and car rentals. Join our community today!</p>
            <div class="socials"></div>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
        
        <div class="footer-section links"></div>
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="services/carpooling.php">Carpooling</a></li>
                <li><a href="services/home-rent.php">Home Rent</a></li>
                <li><a href="services/car-rent.php">Car Rent</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        
        <div class="footer-section contact"></div>
            <h3>Contact Us</h3>
            <p><i class="fas fa-map-marker-alt"></i> TUNIS, BELVEDERE, BUREAU N°1</p>
            <p><i class="fas fa-phone"></i> +216 52 180 466</p>
            <p><i class="fas fa-envelope"></i> info@clyptor.tn</p>
        </div>
    </div>
    
    <div class="footer-bottom"></div>
        <p>&copy; <script>document.write(new Date().getFullYear())</script> Clyptor. All rights reserved.</p>
    </div>
</footer>
<style>
    .post-body{
        color: black;
    }
</style>

<script src="js/main.js"></script>
<script src="js/auth.js"></script>
<script src="js/posts.js"></script>

</body>
</html>