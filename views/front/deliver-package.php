<?php
session_start(); // Ensure this is at the very top of the file
require_once '../../models/PackageDelivery.php';
require_once '../../controllers/PackageDeliveryController.php';

use Models\PackageDelivery;
use Controllers\PackageDeliveryController;

// Database connection
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=clyptor', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch the user's default address if not already set in the session
if (isset($_SESSION['user_id']) && !isset($_SESSION['default_address_id'])) {
    try {
        $stmt = $db->prepare("SELECT address_id FROM user_addresses WHERE user_id = :user_id AND is_default = 1 LIMIT 1");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $defaultAddress = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($defaultAddress) {
            $_SESSION['default_address_id'] = $defaultAddress['address_id'];
        }
    } catch (PDOException $e) {
        error_log("Error fetching default address: " . $e->getMessage());
    }
}

// Fetch the user's addresses for the pickup address dropdown
$userAddresses = [];
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $db->prepare("SELECT address_id, CONCAT(address_line1, ', ', city, ', ', state, ', ', country) AS full_address FROM user_addresses WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $userAddresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching user addresses: " . $e->getMessage());
    }
}

// Initialize model and controller
$model = new PackageDelivery($db);
$controller = new PackageDeliveryController($model); // Pass the model to the controller

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    try {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'You must be logged in to create a post.']);
            exit;
        }

        // Automatically set sender_id and pickup_address_id
        $data = [
            'sender_id' => $_SESSION['user_id'], // Get user ID from session
            'pickup_address_id' => $_POST['pickup_address_id'] ?? $_SESSION['default_address_id'] ?? null, // Get pickup address ID from form or session
            'delivery_address_id' => $_POST['delivery_address_id'] ?? null,
            'package_description' => $_POST['package_description'] ?? null,
            'package_weight' => $_POST['package_weight'] ?? null,
            'package_dimensions' => $_POST['package_dimensions'] ?? null,
            'estimated_value' => $_POST['estimated_value'] ?? null,
            'delivery_deadline' => $_POST['delivery_deadline'] ?? null,
            'proposed_price' => $_POST['proposed_price'] ?? null,
        ];

        if (empty($data['pickup_address_id'])) {
            echo json_encode(['success' => false, 'message' => 'You must set a default pickup address in your profile.']);
            exit;
        }

        // Call the controller to create the post
        $response = $controller->createPost($data);

        // Return the response as JSON
        echo json_encode($response);
        exit;
    } catch (Exception $e) {
        // Log the error and return a generic error message
        error_log("Error creating package delivery: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again later.']);
        exit;
    }
}

// Handle form submission for editing and deleting posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if (!isset($_SESSION['user_id'])) {
            $errorMessage = 'You must be logged in to perform this action.';
        } else {
            $action = $_POST['action'];
            $deliveryId = $_POST['delivery_id'] ?? null;

            if ($action === 'edit' && $deliveryId) {
                $data = [
                    'delivery_id' => $deliveryId,
                    'package_description' => $_POST['package_description'] ?? null,
                    'package_weight' => $_POST['package_weight'] ?? null,
                    'package_dimensions' => $_POST['package_dimensions'] ?? null,
                    'estimated_value' => $_POST['estimated_value'] ?? null,
                    'delivery_deadline' => $_POST['delivery_deadline'] ?? null,
                    'proposed_price' => $_POST['proposed_price'] ?? null,
                ];
                $response = $controller->editPost($data);
                if ($response['success']) {
                    $successMessage = $response['message'];
                } else {
                    $errorMessage = $response['message'];
                }
            } elseif ($action === 'delete' && $deliveryId) {
                $response = $controller->deletePost($deliveryId);
                if ($response['success']) {
                    $successMessage = $response['message'];
                } else {
                    $errorMessage = $response['message'];
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error handling post action: " . $e->getMessage());
        $errorMessage = 'An unexpected error occurred. Please try again later.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deliver Package</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/hf.css">
    <style>
        /* Styles for the posts */
        .posts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .post {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .post:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .post h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .post p {
            margin: 5px 0;
            color: #555;
            font-size: 0.9rem;
        }

        .post .status {
            font-weight: bold;
            color: #007bff;
        }

        .post .price {
            font-weight: bold;
            color: #28a745;
        }

        .post .actions {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .post .actions button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .post .actions button:hover {
            background-color: #0056b3;
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


    <!-- Hero Section -->
    <main class="service-main">
        <section class="service-hero">
            <div class="hero-content">
                <h1>Deliver Packages with Clyptor</h1>
                <p>Fast, secure, and reliable package delivery services at your fingertips.</p>
                <button id="create-post-btn" class="cta-button">Deliver your package</button>
            </div>
            <div class="hero-image">
                <div class="car-animation">
                    
                </div>
            </div>
        </section>
        
    
    <section class="post-form-container" id="post-form-container" style="display:none;">
        <h2>Create a Package Delivery</h2>
        <?php if (isset($successMessage)): ?>
            <p class="success-message"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif (isset($errorMessage)): ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        <form id="post-form" method="POST">
            <input type="hidden" name="create_post" value="1">
            <div class="form-group">
                <label for="pickup_address_id">Pickup Address</label>
                <select id="pickup_address_id" name="pickup_address_id" required>
                    <option value="">Select a pickup address</option>
                    <?php foreach ($userAddresses as $address): ?>
                        <option value="<?= $address['address_id'] ?>" <?= (isset($_SESSION['default_address_id']) && $_SESSION['default_address_id'] == $address['address_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($address['full_address']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="delivery_address_id">Delivery Address ID</label>
                <input type="number" id="delivery_address_id" name="delivery_address_id" required>
            </div>
            <div class="form-group">
                <label for="package_description">Package Description</label>
                <textarea id="package_description" name="package_description" required></textarea>
            </div>
            <div class="form-group">
                <label for="package_weight">Package Weight (kg)</label>
                <input type="number" id="package_weight" name="package_weight" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="package_dimensions">Package Dimensions</label>
                <input type="text" id="package_dimensions" name="package_dimensions">
            </div>
            <div class="form-group">
                <label for="estimated_value">Estimated Value ($)</label>
                <input type="number" id="estimated_value" name="estimated_value" step="0.01">
            </div>
            <div class="form-group">
                <label for="delivery_deadline">Delivery Deadline</label>
                <input type="datetime-local" id="delivery_deadline" name="delivery_deadline">
            </div>
            <div class="form-group">
                <label for="proposed_price">Proposed Price ($)</label>
                <input type="number" id="proposed_price" name="proposed_price" step="0.01">
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">Submit</button>
                <button type="button" id="cancel-post" class="cancel-btn">Cancel</button>
            </div>
        </form>
    </section>
    <section class="posts-section">
        <h2>Available Packages</h2>
        <div id="posts-container" class="posts-container">
            <?php
            $packages = $controller->getAvailablePackages();

            if (!empty($packages)) {
                foreach ($packages as $package) {
                    $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $package['sender_id'];

                    // Debugging output
                    error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'not set'));
                    error_log("Package sender_id: " . $package['sender_id']);
                    error_log("Is owner: " . ($isOwner ? 'true' : 'false'));

                    echo "<div class='post' data-id='{$package['delivery_id']}'>
                            <h3>Package ID: {$package['delivery_id']}</h3>
                            <p><strong>Description:</strong> <span class='description'>{$package['package_description']}</span></p>
                            <p><strong>Weight:</strong> <span class='weight'>{$package['package_weight']}</span> kg</p>
                            <p class='price'><strong>Proposed Price:</strong> $<span class='price-value'>{$package['proposed_price']}</span></p>
                            <p class='status'><strong>Status:</strong> {$package['status']}</p>
                            <div class='actions'>";
                    if ($isOwner) {
                        echo "<button class='edit-btn' data-id='{$package['delivery_id']}'>Edit</button>
                              <form method='POST' style='display:inline-block;'>
                                    <input type='hidden' name='action' value='delete'>
                                    <input type='hidden' name='delivery_id' value='{$package['delivery_id']}'>
                                    <button type='submit' class='delete-btn'>Delete</button>
                              </form>";
                    }
                    echo "      </div>
                          </div>";
                }
            } else {
                echo "<p>No packages available at the moment.</p>";
            }
            ?>
        </div>
    </section>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const postsContainer = document.getElementById("posts-container");
        const postFormContainer = document.getElementById("post-form-container");
        const postForm = document.getElementById("post-form");
        const createPostBtn = document.getElementById("create-post-btn");
        const cancelPostBtn = document.getElementById("cancel-post");

        // Show the popup when "Deliver your package" is clicked
        createPostBtn.addEventListener("click", () => {
            const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
            if (!isLoggedIn) {
                alert("You must be logged in to create a package delivery.");
                return;
            }
            postFormContainer.style.display = "block";
            postForm.reset(); // Reset the form fields
            postForm.dataset.editing = ""; // Clear editing state
        });

        // Hide the popup when "Cancel" is clicked
        cancelPostBtn.addEventListener("click", () => {
            postFormContainer.style.display = "none";
            postForm.reset(); // Reset the form fields
        });

        // Handle Edit button click
        postsContainer.addEventListener("click", (e) => {
            if (e.target.classList.contains("edit-btn")) {
                const postElement = e.target.closest(".post");
                const deliveryId = postElement.dataset.id;
                const description = postElement.querySelector(".description").textContent;
                const weight = postElement.querySelector(".weight").textContent;
                const price = postElement.querySelector(".price-value").textContent;

                // Populate the form with post details for editing
                document.getElementById("package_description").value = description;
                document.getElementById("package_weight").value = weight;
                document.getElementById("proposed_price").value = price;

                // Set the editing state
                postForm.dataset.editing = deliveryId;

                // Show the form for editing
                postFormContainer.style.display = "block";
            }
        });

        // Handle form submission using AJAX
        postForm.addEventListener("submit", (e) => {
            e.preventDefault(); // Prevent the default form submission

            const formData = new FormData(postForm);
            const editingId = postForm.dataset.editing;

            if (editingId) {
                // Editing an existing post
                formData.append("action", "edit");
                formData.append("delivery_id", editingId);
            } else {
                // Creating a new post
                formData.append("create_post", "1");
            }

            fetch("", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((result) => {
                    if (result.success) {
                        alert(result.message);
                        // Close the form popup
                        postFormContainer.style.display = "none";

                        // Reset the form fields
                        postForm.reset();

                        // Update the post in the DOM if editing
                        if (editingId) {
                            const postElement = postsContainer.querySelector(`.post[data-id='${editingId}']`);
                            postElement.querySelector(".description").textContent = formData.get("package_description");
                            postElement.querySelector(".weight").textContent = formData.get("package_weight");
                            postElement.querySelector(".price-value").textContent = formData.get("proposed_price");
                        } else {
                            // Optionally, reload the posts to reflect the new post
                            fetchPosts();
                        }
                    } else {
                        alert(result.message);
                    }
                })
                .catch((error) => {
                    console.error("Error creating/editing post:", error);
                    alert("An error occurred while processing the post. Please try again.");
                });
        });

        // Function to fetch and display posts
        function fetchPosts() {
            fetch("")
                .then((response) => response.text())
                .then((html) => {
                    // Replace the posts container with the updated content
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, "text/html");
                    const updatedPostsContainer = doc.getElementById("posts-container");
                    postsContainer.innerHTML = updatedPostsContainer.innerHTML;
                })
                .catch((error) => {
                    console.error("Error fetching posts:", error);
                });
        }
    });
    </script>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <div class="logo-container">
                    <a href="index.php" class="logo">
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
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const authButtons = document.querySelector(".auth-buttons");
        const isLoggedIn = localStorage.getItem("isLoggedIn") === "true";

        if (isLoggedIn) {
            authButtons.innerHTML = `
                <a href="../admin/user/user-dashboard.php" class="btn btn-primary">Dashboard</a>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            `;
        }

        // Add functionality to the "Deliver your package" button
        const createPostBtn = document.getElementById("create-post-btn");
        const postFormContainer = document.getElementById("post-form-container");
        const cancelPostBtn = document.getElementById("cancel-post");

        // Show the popup when "Deliver your package" is clicked
        createPostBtn.addEventListener("click", () => {
            const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
            if (!isLoggedIn) {
                alert("You must be logged in to create a package delivery.");
                return;
            }
            postFormContainer.style.display = "block";
            window.scrollTo({ top: postFormContainer.offsetTop, behavior: "smooth" });
        });

        // Hide the popup when "Cancel" is clicked
        cancelPostBtn.addEventListener("click", () => {
            postFormContainer.style.display = "none";
        });
    });
    </script>
</body>
</html>