<?php
require_once '../../controllers/HomeRentalsController.php';
require_once '../../config/Database.php';

$pdo = (new Database())->getConnection();
$controller = new HomeRentalsController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $result = $controller->deletePropertyListing($_POST['rental_id']);
    $message = $result ? "Property deleted successfully!" : "Failed to delete property.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {
    $result = $controller->updatePropertyListing($_POST, $_FILES);
    $message = $result ? "Property updated successfully!" : "Failed to update property.";
}

// Fetch all posts to display in the admin panel
$posts = $controller->fetchPropertyListings();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Admin - Home Rentals</title>
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
                <a href="admin.php">
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
                <a href="adminrent.php" class="active">
                    <span class="material-icons-sharp">
                        home
                    </span>
                    <h3>Home Rentals</h3>
                </a>
                <a href="admincar.php">
                    <span class="material-icons-sharp">
                        directions_car
                    </span>
                    <h3>Car Rentals</h3>
                </a>
                <a href="adminpackage.php">
                    <span class="material-icons-sharp">
                        local_shipping
                    </span>
                    <h3>Deliver Packages</h3>
                </a>
                <a href="ticket.php">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Tickets</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
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
            <h1>Home Rentals</h1>
            <?php if (isset($message)): ?>
                <div class="message">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>
            <div class="recent-orders">
                <h2>All Home Rental Posts</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Price/Night</th>
                            <th>Bedrooms</th>
                            <th>Bathrooms</th>
                            <th>Max Guests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?= htmlspecialchars($post['title']) ?></td>
                                    <td><?= htmlspecialchars($post['property_type']) ?></td>
                                    <td>$<?= htmlspecialchars($post['daily_rate']) ?></td>
                                    <td><?= htmlspecialchars($post['bedrooms']) ?></td>
                                    <td><?= htmlspecialchars($post['bathrooms']) ?></td>
                                    <td><?= htmlspecialchars($post['max_guests']) ?></td>
                                    <td><?= htmlspecialchars($post['status']) ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="rental_id" value="<?= htmlspecialchars($post['rental_id']) ?>">
                                            <button type="submit" name="delete_post" class="action-btn delete-btn">Delete</button>
                                        </form>
                                        <button class="action-btn edit-btn" onclick="editPost(<?= htmlspecialchars(json_encode($post)) ?>)">Edit</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No properties available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <!-- End of Main Content -->

        <!-- Popup for editing a post -->
        <div class="popup-overlay" id="popup-overlay" style="display: none;">
            <div class="popup-content">
                <button class="close-btn" id="close-popup">&times;</button>
                <h2 id="popup-title">Edit Property</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="rental_id" id="rental-id">
                    <input type="hidden" name="existing_photo" id="existing-photo">
                    <div class="form-group">
                        <label for="post-title">Property Title</label>
                        <input type="text" id="post-title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="post-description">Description</label>
                        <textarea id="post-description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="post-type">Property Type</label>
                        <select id="post-type" name="property_type" required>
                            <option value="apartment">Apartment</option>
                            <option value="house">House</option>
                            <option value="villa">Villa</option>
                            <option value="condo">Condo</option>
                            <option value="room">Room</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="post-bedrooms">Bedrooms</label>
                        <input type="number" id="post-bedrooms" name="bedrooms" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="post-bathrooms">Bathrooms</label>
                        <input type="number" id="post-bathrooms" name="bathrooms" min="0" step="0.5" required>
                    </div>
                    <div class="form-group">
                        <label for="post-guests">Max Guests</label>
                        <input type="number" id="post-guests" name="max_guests" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="post-price">Price per Night ($)</label>
                        <input type="number" id="post-price" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="post-available-from">Available From</label>
                        <input type="date" id="post-available-from" name="available_from" required>
                    </div>
                    <div class="form-group">
                        <label for="post-available-to">Available To</label>
                        <input type="date" id="post-available-to" name="available_to">
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="edit_post" class="submit-btn">Update Property</button>
                        <button type="button" class="cancel-btn" id="cancel-popup">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function editPost(post) {
                const popupOverlay = document.getElementById('popup-overlay');
                popupOverlay.style.display = 'flex';

                document.getElementById('rental-id').value = post.rental_id;
                document.getElementById('post-title').value = post.title;
                document.getElementById('post-description').value = post.description;
                document.getElementById('post-type').value = post.property_type;
                document.getElementById('post-bedrooms').value = post.bedrooms;
                document.getElementById('post-bathrooms').value = post.bathrooms;
                document.getElementById('post-guests').value = post.max_guests;
                document.getElementById('post-price').value = post.daily_rate;
                document.getElementById('post-available-from').value = post.available_from;
                document.getElementById('post-available-to').value = post.available_to || '';
            }

            document.getElementById('cancel-popup').addEventListener('click', function () {
                document.getElementById('popup-overlay').style.display = 'none';
            });

            document.getElementById('close-popup').addEventListener('click', function () {
                document.getElementById('popup-overlay').style.display = 'none';
            });
        </script>
    </div>
</body>

</html>
