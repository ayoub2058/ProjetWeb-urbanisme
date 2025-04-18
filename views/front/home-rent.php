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

// Include the controller
require_once '../../controllers/HomeRentalsController.php';
require_once '../../config/Database.php';

$pdo = (new Database())->getConnection();
$controller = new HomeRentalsController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $result = $controller->createPropertyListing($_POST, $_FILES);
    $message = $result ? "Property listed successfully!" : "Failed to list property.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $result = $controller->deletePropertyListing($_POST['rental_id']);
    $message = $result ? "Property deleted successfully!" : "Failed to delete property.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {
    $result = $controller->updatePropertyListing($_POST, $_FILES);
    $message = $result ? "Property updated successfully!" : "Failed to update property.";
}

// Fetch all posts to display in the posts section
$posts = $controller->fetchPropertyListings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clyptor - Home Rent</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home-rent.css">
    <link rel="stylesheet" href="css/home-rent1.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.js"></script>
    <style>
/* ...existing code... */
.post-card {
    width: 100%; /* Adjust width to make it responsive */
    max-width: 400px; /* Increase max width for better visibility */
    margin: 20px auto; /* Center the card */
    padding: 20px; /* Add padding for better spacing */
    border: 1px solid #ddd; /* Add a border for better separation */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow for depth */
    background-color: #fff; /* Ensure background is white */
}

.post-card .post-header {
    margin-bottom: 15px; /* Add spacing below the header */
}

.post-card .post-actions {
    display: flex;
    justify-content: space-between; /* Space out the buttons */
    margin-top: 15px; /* Add spacing above the buttons */
}

.post-card .action-btn {
    padding: 10px 15px; /* Increase button size */
    font-size: 14px; /* Adjust font size for better readability */
    border-radius: 5px; /* Rounded buttons */
    cursor: pointer; /* Add pointer cursor */
}

.post-card .action-btn.view-details-btn {
    background-color: #007bff; /* Blue background for "View Details" */
    color: #fff; /* White text */
    border: none;
}

.post-card .action-btn.delete-btn {
    background-color: #dc3545; /* Red background for "Delete" */
    color: #fff; /* White text */
    border: none;
}

.post-card .action-btn:hover {
    opacity: 0.9; /* Slight hover effect */
}

.post-image img {
    width: 100%; /* Ensure image fits the card */
    height: auto; /* Maintain aspect ratio */
    border-radius: 10px; /* Match card's rounded corners */
}

/* Enhanced Popup Styles */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6); /* Darker background for better focus */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    transition: opacity 0.3s ease-in-out;
}

.popup-content {
    background: #ffffff;
    padding: 40px;
    border-radius: 15px;
    width: 90%;
    max-width: 800px; /* Increased width for better usability */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25); /* Stronger shadow for depth */
    animation: popup-slide-in 0.3s ease-out;
    position: relative;
    overflow-y: auto; /* Allow scrolling if content overflows */
    max-height: 90%; /* Prevent the popup from exceeding the viewport height */
}

.popup-content h2 {
    margin-top: 0;
    font-size: 28px; /* Larger font size for better visibility */
    color: #333;
    text-align: center;
    border-bottom: 2px solid #007bff;
    padding-bottom: 15px;
}

.popup-content .form-group {
    margin-bottom: 20px;
}

.popup-content .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #555;
}

.popup-content .form-group input,
.popup-content .form-group textarea,
.popup-content .form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.popup-content .form-group input:focus,
.popup-content .form-group textarea:focus,
.popup-content .form-group select:focus {
    border-color: #007bff;
    outline: none;
}

.popup-content .form-actions {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.popup-content .form-actions button {
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.popup-content .form-actions .submit-btn {
    background-color: #007bff;
    color: #fff;
}

.popup-content .form-actions .submit-btn:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

.popup-content .form-actions .cancel-btn {
    background-color: #dc3545;
    color: #fff;
}

.popup-content .form-actions .cancel-btn:hover {
    background-color: #a71d2a;
    transform: scale(1.05);
}

.popup-content .close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 20px;
    color: #333;
    cursor: pointer;
    transition: color 0.3s ease;
}

.popup-content .close-btn:hover {
    color: #007bff;
}

/* Popup Animation */
@keyframes popup-slide-in {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
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
        <section class="service-hero">
            <div class="spline-container">
<spline-viewer url="https://prod.spline.design/NOspby6AJwzuaFUg/scene.splinecode"></spline-viewer></div>
            <div class="hero-content">
                <h1>Home Rental</h1>
                <p>Find or offer vacation rentals, apartments, and rooms for rent.</p>
                <button id="create-post-btn" class="cta-button">List Your Property</button>
            </div>
            <div class="hero-image">
                <div class="house-animation">
                    
                </div>
            </div>
        </section>

        <?php if (isset($message)): ?>
            <div class="message">
                <p><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endif; ?>

        <!-- Popup for creating/editing a post -->
        <div class="popup-overlay" id="popup-overlay" style="display: none;">
            <div class="popup-content">
                <button class="close-btn" id="close-popup">&times;</button>
                <h2 id="popup-title">List Your Property</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="rental_id" id="rental-id">
                    <input type="hidden" name="existing_photo" id="existing-photo">
                    <!-- Hidden fields for owner_id and address_id -->
                    <input type="hidden" name="owner_id" value="1"> <!-- Replace with dynamic user ID -->
                    <input type="hidden" name="address_id" value="1"> <!-- Replace with dynamic address ID -->

                    <div class="form-group">
                        <label for="post-title">Property Title</label>
                        <input type="text" id="post-title" name="title" placeholder="e.g., Cozy downtown apartment" required>
                    </div>
                    <div class="form-group">
                        <label for="post-description">Description</label>
                        <textarea id="post-description" name="description" placeholder="Describe your property, amenities, and any rules..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="post-images">Property Images (up to 5)</label>
                        <input type="file" id="post-images" name="images[]" accept="image/*" multiple>
                    </div>
                    <div class="form-group">
                        <label for="post-address">Address</label>
                        <input type="text" id="post-address" name="address" placeholder="Full address" required>
                    </div>
                    <div class="form-group">
                        <label for="post-type">Property Type</label>
                        <select id="post-type" name="property_type" required>
                            <option value="apartment">Apartment</option>
                            <option value="house">House</option>
                            <option value="room">Room</option>
                            <option value="villa">Villa</option>
                            <option value="cottage">Cottage</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="post-bedrooms">Bedrooms</label>
                        <input type="number" id="post-bedrooms" name="bedrooms" min="0" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="post-bathrooms">Bathrooms</label>
                        <input type="number" id="post-bathrooms" name="bathrooms" min="0" step="0.5" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="post-guests">Max Guests</label>
                        <input type="number" id="post-guests" name="max_guests" min="1" value="2" required>
                    </div>
                    <div class="form-group">
                        <label for="post-price">Price per Night ($)</label>
                        <input type="number" id="post-price" name="price" min="0" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="post-amenities">Amenities</label>
                        <div>
                            <label><input type="checkbox" name="amenities[]" value="wifi"> WiFi</label>
                            <label><input type="checkbox" name="amenities[]" value="kitchen"> Kitchen</label>
                            <label><input type="checkbox" name="amenities[]" value="parking"> Parking</label>
                            <label><input type="checkbox" name="amenities[]" value="tv"> TV</label>
                            <label><input type="checkbox" name="amenities[]" value="ac"> Air Conditioning</label>
                            <label><input type="checkbox" name="amenities[]" value="washer"> Washer</label>
                            <label><input type="checkbox" name="amenities[]" value="pool"> Pool</label>
                            <label><input type="checkbox" name="amenities[]" value="pets"> Pets Allowed</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="post-available-from">Available From</label>
                        <input type="date" id="post-available-from" name="available_from" required>
                    </div>
                    <div class="form-group">
                        <label for="post-available-to">Available To</label>
                        <input type="date" id="post-available-to" name="available_to">
                    </div>
                    <div class="form-group">
                        <label for="post-minimum-stay">Minimum Stay (days)</label>
                        <input type="number" id="post-minimum-stay" name="minimum_stay" min="1" value="1" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="edit_post" id="edit-post-btn" class="submit-btn" style="display: none;">Update Property</button>
                        <button type="submit" name="create_post" id="create-post-btn" class="submit-btn">List Property</button>
                        <button type="button" class="cancel-btn" id="cancel-popup">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <section class="posts-section">
            <h2>Available Properties</h2>
            <div class="filter-controls">
                <select id="filter-type">
                    <option value="all">All Types</option>
                    <option value="apartment">Apartment</option>
                    <option value="house">House</option>
                    <option value="room">Room</option>
                    <option value="villa">Villa</option>
                    <option value="cottage">Cottage</option>
                </select>
                <select id="filter-price">
                    <option value="all">Any Price</option>
                    <option value="0-50">$0 - $50</option>
                    <option value="50-100">$50 - $100</option>
                    <option value="100-200">$100 - $200</option>
                    <option value="200+">$200+</option>
                </select>
                <select id="filter-bedrooms">
                    <option value="all">Any Bedrooms</option>
                    <option value="1">1 Bedroom</option>
                    <option value="2">2 Bedrooms</option>
                    <option value="3">3+ Bedrooms</option>
                </select>
            </div>
            <div id="posts-container" class="posts-container">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post-card">
                            <div class="post-header">
                                <h3><?= htmlspecialchars($post['title']) ?></h3>
                                <span class="post-category"><?= htmlspecialchars($post['property_type']) ?></span>
                                <span class="post-price">$<?= htmlspecialchars($post['daily_rate']) ?>/night</span>
                            </div>
                            <div class="post-image">
                                <img src="../uploads/<?= htmlspecialchars($post['main_photo_url'] ?? 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                            </div>
                            <div class="post-content">
                                <p><strong>Description:</strong> <?= htmlspecialchars($post['description']) ?></p>
                                <p><strong>Address:</strong> <?= htmlspecialchars($post['address_id']) ?></p>
                                <p><strong>Bedrooms:</strong> <?= htmlspecialchars($post['bedrooms']) ?></p>
                                <p><strong>Bathrooms:</strong> <?= htmlspecialchars($post['bathrooms']) ?></p>
                                <p><strong>Max Guests:</strong> <?= htmlspecialchars($post['max_guests']) ?></p>
                                <p><strong>Amenities:</strong> <?= htmlspecialchars($post['amenities']) ?></p>
                                <p><strong>Available From:</strong> <?= htmlspecialchars($post['available_from']) ?></p>
                                <p><strong>Available To:</strong> <?= htmlspecialchars($post['available_to'] ?? 'N/A') ?></p>
                                <p><strong>Minimum Stay:</strong> <?= htmlspecialchars($post['minimum_stay']) ?> days</p>
                            </div>
                            <div class="post-footer">
                                <span class="post-author">Posted by Owner ID: <?= htmlspecialchars($post['owner_id']) ?></span>
                                <span class="post-date"><?= htmlspecialchars(date('F j, Y', strtotime($post['created_at']))) ?></span>
                            </div>
                            <div class="post-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="rental_id" value="<?= htmlspecialchars($post['rental_id']) ?>">
                                    <button type="submit" name="delete_post" class="action-btn delete-btn">Delete</button>
                                </form>
                                <button class="action-btn edit-btn" onclick="editPost(<?= htmlspecialchars(json_encode($post)) ?>)">Edit</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No properties available. List your property now!</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <div class="logo-container">
                    <a href="index.php" class="logo">
                        <!-- <img src="assets/images/logo.png" alt="Clyptor Logo"> -->
                        <span class="logo-text">Clyptor</span>
                    </a>
                </div>
                <p>Clyptor provides innovative solutions for carpooling, home rentals, and car rentals. Join our community today!</p>
                <div class="socials">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="services/carpooling.php">Carpooling</a></li>
                    <li><a href="services/home-rent.php">Home Rent</a></li>
                    <li><a href="services/car-rent.php">Car Rent</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section contact">
                <h3>Contact Us</h3>
                <p><i class="fas fa-map-marker-alt"></i> TUNIS, BELVEDERE, BUREAU N°1</p>
                <p><i class="fas fa-phone"></i> +216 52 180 466</p>
                <p><i class="fas fa-envelope"></i> info@clyptor.tn</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <script>document.write(new Date().getFullYear())</script> Clyptor. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/posts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const createPostBtn = document.getElementById('create-post-btn');
            const popupOverlay = document.getElementById('popup-overlay');
            const cancelPopupBtn = document.getElementById('cancel-popup');
            const closePopupBtn = document.getElementById('close-popup');

            createPostBtn.addEventListener('click', function () {
                popupOverlay.style.display = 'flex';
                document.getElementById('popup-title').textContent = 'List Your Property';
                document.getElementById('create-post-btn').style.display = 'inline-block';
                document.getElementById('edit-post-btn').style.display = 'none';
            });

            cancelPopupBtn?.addEventListener('click', function () {
                popupOverlay.style.display = 'none';
            });

            closePopupBtn?.addEventListener('click', function () {
                popupOverlay.style.display = 'none';
            });
        });

        function editPost(post) {
            const popupOverlay = document.getElementById('popup-overlay');
            popupOverlay.style.display = 'flex';

            document.getElementById('popup-title').textContent = 'Edit Your Property';
            document.getElementById('rental-id').value = post.rental_id;
            document.getElementById('existing-photo').value = post.main_photo_url;

            document.getElementById('post-title').value = post.title;
            document.getElementById('post-description').value = post.description;
            document.getElementById('post-address').value = post.address_id;
            document.getElementById('post-type').value = post.property_type;
            document.getElementById('post-bedrooms').value = post.bedrooms;
            document.getElementById('post-bathrooms').value = post.bathrooms;
            document.getElementById('post-guests').value = post.max_guests;
            document.getElementById('post-price').value = post.daily_rate;
            document.getElementById('post-available-from').value = post.available_from;
            document.getElementById('post-available-to').value = post.available_to || '';
            document.getElementById('post-minimum-stay').value = post.minimum_stay;

            const amenities = post.amenities ? post.amenities.split(',') : [];
            document.querySelectorAll('input[name="amenities[]"]').forEach(checkbox => {
                checkbox.checked = amenities.includes(checkbox.value);
            });

            document.getElementById('create-post-btn').style.display = 'none';
            document.getElementById('edit-post-btn').style.display = 'inline-block';
        }
    </script>
</body>
</html>