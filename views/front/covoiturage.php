<?php
require_once '../../controllers/CovoiturageController.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action.']);
        exit;
    }

    $controller = new CovoiturageController();
    $action = $_GET['action'] ?? 'create';

    if ($action === 'create') {
        $result = $controller->createRideOffer($_POST, $_FILES);
    } elseif ($action === 'edit') {
        $result = $controller->editRideOffer($_POST);
    } elseif ($action === 'delete') {
        $result = $controller->deleteRideOffer($_POST['ride_id']);
    }

    echo json_encode($result);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $controller = new CovoiturageController();
    $result = $controller->fetchRideOffers();
    echo json_encode($result);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clyptor - Covoiturage</title>
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
        <section class="service-hero">
            <div class="hero-content">
                <h1>Covoiturage</h1>
                <p>Share rides and save money on your daily commute or long trips.</p>
                <button id="create-post-btn" class="cta-button">Create Ride Offer</button>
            </div>
            <div class="hero-image">
                <div class="car-animation">
                    
                </div>
            </div>
        </section>

        <section class="post-form-container" id="post-form-container" style="display:none;">
            <h2>Create a Ride Offer</h2>
            <form id="post-form" class="post-form" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="post-title">Trip Title</label>
                    <input type="text" id="post-title" name="title" placeholder="e.g., Daily commute to downtown" required>
                </div>
                <div class="form-group">
                    <label for="post-description">Details</label>
                    <textarea id="post-description" name="description" placeholder="Describe your route, schedule, and any preferences..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="post-image">Vehicle Image</label>
                    <input type="file" id="post-image" name="image" accept="image/*">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="post-departure">Departure</label>
                        <input type="text" id="post-departure" name="departure" placeholder="Starting location" required>
                    </div>
                    <div class="form-group">
                        <label for="post-destination">Destination</label>
                        <input type="text" id="post-destination" name="destination" placeholder="Destination" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="post-date">Date</label>
                        <input type="date" id="post-date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="post-time">Time</label>
                        <input type="time" id="post-time" name="time" required>
                    </div>
                    <div class="form-group">
                        <label for="post-seats">Available Seats</label>
                        <input type="number" id="post-seats" name="seats" min="1" max="10" value="1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="post-price">Price per Seat ($)</label>
                    <input type="number" id="post-price" name="price" min="0" step="0.01" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label for="post-category">Category</label>
                    <select id="post-category" name="category" required>
                        <option value="daily">Daily Commute</option>
                        <option value="weekend">Weekend Trip</option>
                        <option value="long-distance">Long Distance</option>
                        <option value="airport">Airport Transfer</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Post Offer</button>
                    <button type="button" id="cancel-post" class="cancel-btn">Cancel</button>
                </div>
            </form>
        </section>

        <section class="posts-section">
            <h2>Available Rides</h2>
            <div class="filter-controls">
                <select id="filter-category">
                    <option value="all">All Categories</option>
                    <option value="daily">Daily Commute</option>
                    <option value="weekend">Weekend Trip</option>
                    <option value="long-distance">Long Distance</option>
                    <option value="airport">Airport Transfer</option>
                </select>
                <select id="filter-date">
                    <option value="all">Any Date</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
            <div id="posts-container" class="posts-container">
                <!-- Posts will be loaded here -->
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
            const postForm = document.getElementById('post-form');
            const postsContainer = document.getElementById('posts-container');
            const createPostBtn = document.getElementById('create-post-btn');
            const postFormContainer = document.getElementById('post-form-container');
            const cancelPostBtn = document.getElementById('cancel-post');
            const filterCategory = document.getElementById('filter-category');
            const filterDate = document.getElementById('filter-date');

            // Show the popup when "Create Ride Offer" is clicked
            createPostBtn.addEventListener('click', function () {
                if (!localStorage.getItem("isLoggedIn") || localStorage.getItem("isLoggedIn") !== "true") {
                    alert("You must be logged in to create a ride offer.");
                    return;
                }
                postFormContainer.style.display = 'block';
                window.scrollTo({ top: postFormContainer.offsetTop, behavior: 'smooth' });
            });

            // Hide the popup when "Cancel" is clicked
            if (cancelPostBtn) {
                cancelPostBtn.addEventListener('click', function () {
                    postFormContainer.style.display = 'none';
                });
            }

            // Fetch and display posts
            function fetchPosts() {
                fetch('../../controllers/CovoiturageController.php?action=fetch')
                    .then(response => response.json())
                    .then(posts => {
                        const selectedCategory = filterCategory.value;
                        const selectedDate = filterDate.value;

                        // Filter posts based on selected filters
                        const filteredPosts = posts.filter(post => {
                            let matchesCategory = selectedCategory === 'all' || post.category?.toLowerCase() === selectedCategory.toLowerCase();
                            let matchesDate = true;

                            if (selectedDate === 'today') {
                                const today = new Date().toISOString().split('T')[0];
                                matchesDate = post.departure_datetime.startsWith(today);
                            } else if (selectedDate === 'week') {
                                const today = new Date();
                                const weekFromToday = new Date();
                                weekFromToday.setDate(today.getDate() + 7);
                                const postDate = new Date(post.departure_datetime);
                                matchesDate = postDate >= today && postDate <= weekFromToday;
                            } else if (selectedDate === 'month') {
                                const today = new Date();
                                const monthFromToday = new Date();
                                monthFromToday.setMonth(today.getMonth() + 1);
                                const postDate = new Date(post.departure_datetime);
                                matchesDate = postDate >= today && postDate <= monthFromToday;
                            }

                            return matchesCategory && matchesDate;
                        });

                        // Render filtered posts
                        postsContainer.innerHTML = '';
                        filteredPosts.forEach(post => {
                            const postElement = document.createElement('div');
                            postElement.className = 'post-card';
                            postElement.innerHTML = `
                                <div class="post-header">
                                    <h3>${post.title}</h3>
                                    <span class="post-category">${post.category || 'N/A'}</span>
                                </div>
                                ${post.image ? `<div class="post-image"><img src="${post.image}" alt="${post.title}"></div>` : ''}
                                <div class="post-content">
                                    <p><strong>Departure:</strong> ${post.departure_address}</p>
                                    <p><strong>Destination:</strong> ${post.destination_address}</p>
                                    <p><strong>Date & Time:</strong> ${new Date(post.departure_datetime).toLocaleString()}</p>
                                    <p><strong>Seats Available:</strong> ${post.available_seats}</p>
                                    <p><strong>Price per Seat:</strong> $${post.price_per_seat}</p>
                                    <p>${post.additional_notes || ''}</p>
                                </div>
                                <div class="post-footer">
                                    <span class="post-author">Posted by ${post.user_name}</span>
                                    <span class="post-date">${new Date(post.created_at).toLocaleDateString()}</span>
                                </div>
                                <div class="post-actions">
                                    ${post.is_owner ? `
                                        <button class="action-btn edit-btn" data-id="${post.ride_id}">Edit</button>
                                        <button class="action-btn delete-btn" data-id="${post.ride_id}">Delete</button>
                                    ` : ''}
                                    <button class="action-btn reserve-btn" data-id="${post.ride_id}" data-price="${post.price_per_seat}">Reserve</button>
                                    <button class="action-btn contact-btn" data-email="${post.user_email}">Contact</button>
                                </div>
                            `;
                            postsContainer.appendChild(postElement);
                        });

                        // Add event listeners for edit, delete, reserve, and contact buttons
                        document.querySelectorAll('.edit-btn').forEach(button => {
                            button.addEventListener('click', () => {
                                if (!localStorage.getItem("isLoggedIn") || localStorage.getItem("isLoggedIn") !== "true") {
                                    alert("You must be logged in to edit a ride offer.");
                                    return;
                                }
                                const rideId = button.dataset.id;
                                const ride = posts.find(p => p.ride_id == rideId);
                                if (ride) {
                                    // Populate the form with ride details for editing
                                    document.getElementById('post-title').value = ride.title;
                                    document.getElementById('post-description').value = ride.additional_notes;
                                    document.getElementById('post-departure').value = ride.departure_address;
                                    document.getElementById('post-destination').value = ride.destination_address;
                                    document.getElementById('post-date').value = ride.departure_datetime.split(' ')[0];
                                    document.getElementById('post-time').value = ride.departure_datetime.split(' ')[1];
                                    document.getElementById('post-seats').value = ride.available_seats;
                                    document.getElementById('post-price').value = ride.price_per_seat;

                                    // Show the form for editing
                                    postFormContainer.style.display = 'block';

                                    // Handle form submission for editing
                                    postForm.onsubmit = function (e) {
                                        e.preventDefault();
                                        const formData = new FormData(postForm);
                                        formData.append('ride_id', rideId);
                                        formData.append('driver_id', 1); // Replace with actual logged-in user ID

                                        fetch('../../controllers/CovoiturageController.php?action=edit', {
                                            method: 'POST',
                                            body: formData
                                        })
                                            .then(response => response.json())
                                            .then(result => {
                                                if (result.success) {
                                                    alert('Ride offer updated successfully!');
                                                    fetchPosts();
                                                    postForm.reset();
                                                    postFormContainer.style.display = 'none';
                                                } else {
                                                    alert('Error: ' + result.message);
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error editing ride:', error);
                                                alert('An error occurred while editing the ride. Please try again.');
                                            });
                                    };
                                }
                            });
                        });

                        document.querySelectorAll('.delete-btn').forEach(button => {
                            button.addEventListener('click', () => {
                                if (!localStorage.getItem("isLoggedIn") || localStorage.getItem("isLoggedIn") !== "true") {
                                    alert("You must be logged in to delete a ride offer.");
                                    return;
                                }
                                const rideId = button.dataset.id;
                                if (confirm('Are you sure you want to delete this post?')) {
                                    fetch(`../../controllers/CovoiturageController.php?action=delete&id=${rideId}`, { method: 'DELETE' })
                                        .then(response => response.json())
                                        .then(result => {
                                            if (result.success) {
                                                alert('Post deleted successfully!');
                                                fetchPosts();
                                            } else {
                                                alert('Error: ' + result.message);
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error deleting post:', error);
                                            alert('An error occurred while deleting the post. Please try again.');
                                        });
                                }
                            });
                        });

                        document.querySelectorAll('.reserve-btn').forEach(button => {
                            button.addEventListener('click', () => {
                                const rideId = button.getAttribute('data-id'); // Ensure rideId is correctly retrieved
                                const pricePerSeat = button.getAttribute('data-price');
                                const seatsToBook = prompt('Enter the number of seats to reserve:');
                                if (seatsToBook && !isNaN(seatsToBook) && seatsToBook > 0) {
                                    const totalAmount = seatsToBook * pricePerSeat;

                                    console.log('Ride ID:', rideId); // Debugging: Log ride_id to ensure it's being retrieved
                                    console.log('Seats to Book:', seatsToBook);
                                    console.log('Total Amount:', totalAmount);

                                    fetch('../../controllers/CovoiturageController.php?action=reserve', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({
                                            ride_id: rideId, // Pass the correct ride_id
                                            passenger_id: 1, // Replace with actual logged-in user ID
                                            seats_booked: seatsToBook,
                                            total_amount: totalAmount
                                        })
                                    })
                                        .then(response => response.json())
                                        .then(result => {
                                            if (result.success) {
                                                alert('Seats reserved successfully!');
                                                fetchPosts();
                                            } else {
                                                alert('Error: ' + result.message);
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error reserving seats:', error);
                                            alert('An error occurred while reserving seats. Please try again.');
                                        });
                                } else {
                                    alert('Please enter a valid number of seats.');
                                }
                            });
                        });

                        document.querySelectorAll('.contact-btn').forEach(button => {
                            button.addEventListener('click', () => {
                                const email = button.dataset.email;
                                window.location.href = `mailto:${email}?subject=Ride Inquiry&body=Hi, I am interested in your ride offer.`;
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching posts:', error);
                    });
            }

            // Add event listeners for filter changes
            filterCategory.addEventListener('change', fetchPosts);
            filterDate.addEventListener('change', fetchPosts);

            // Handle post creation
            postForm.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!localStorage.getItem("isLoggedIn") || localStorage.getItem("isLoggedIn") !== "true") {
                    alert("You must be logged in to create a ride offer.");
                    return;
                }
                const formData = new FormData(postForm);

                fetch('../../controllers/CovoiturageController.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('Ride offer created successfully!');
                            fetchPosts();
                            postForm.reset();
                            postFormContainer.style.display = 'none';
                        } else {
                            alert('Error: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting form:', error);
                        alert('An error occurred while submitting the form. Please try again.');
                    });
            });

            // Handle post deletion
            function deletePost(id) {
                if (confirm('Are you sure you want to delete this post?')) {
                    fetch(`../../controllers/CovoiturageController.php?action=delete&id=${id}`, { method: 'DELETE' })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                alert('Post deleted successfully!');
                                fetchPosts();
                            } else {
                                alert('Error: ' + result.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting post:', error);
                            alert('An error occurred while deleting the post. Please try again.');
                        });
                }
            }

            // Handle post editing (simplified for demonstration)
            function editPost(id) {
                alert('Edit functionality not implemented yet.');
            }

            // Initial fetch
            fetchPosts();
        });

        document.addEventListener("DOMContentLoaded", () => {
            const authButtons = document.querySelector(".auth-buttons");
            const isLoggedIn = localStorage.getItem("isLoggedIn") === "true";

            if (isLoggedIn) {
                authButtons.innerHTML = `
                    <a href="../admin/user/user-dashboard.php" class="btn btn-primary">Dashboard</a>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                `;
            }
        });
    </script>
</body>
</html>