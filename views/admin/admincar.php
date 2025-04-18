<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Clyptor - Admin Car Rentals</title>
    <style>
        .post-form-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 90%;
            max-width: 500px;
        }

        .popup-content h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            text-align: center;
        }

        .post-form .form-group {
            margin-bottom: 15px;
        }

        .post-form .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .post-form .form-group input,
        .post-form .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .post-form .form-actions {
            display: flex;
            justify-content: space-between;
        }

        .post-form .submit-btn,
        .post-form .cancel-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .post-form .submit-btn {
            background-color: #28a745;
            color: #fff;
        }

        .post-form .cancel-btn {
            background-color: #dc3545;
            color: #fff;
        }

        .image-preview img {
            max-width: 100px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
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
                <a href="admincar.php" class="active">
                    <span class="material-icons-sharp">
                        directions_car
                    </span>
                    <h3>Car Rentals</h3>
                </a>
                <a href="ticket.php">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Tickets</h3>
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
            <h1>Car Rentals</h1>
            <button id="create-post-btn" class="cta-button">List Vehicle</button>
            <section class="post-form-container" id="post-form-container" style="display:none;">
                <div class="popup-content">
                    <h2>List Your Vehicle</h2>
                    <form id="post-form" class="post-form">
                        <div class="form-group">
                            <label for="post-title">Vehicle Title</label>
                            <input type="text" id="post-title" name="title" placeholder="e.g., 2020 Toyota Camry" required>
                        </div>
                        <div class="form-group">
                            <label for="post-description">Description</label>
                            <textarea id="post-description" name="description" placeholder="Describe your vehicle, features, and rental terms..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="post-images">Vehicle Images (up to 5)</label>
                            <input type="file" id="post-images" name="images[]" accept="image/*" multiple>
                            <div id="image-preview" class="image-preview"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="post-daily-rate">Price per Day ($)</label>
                                <input type="number" id="post-daily-rate" name="daily_rate" min="0" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="form-group">
                                <label for="post-location">Location</label>
                                <input type="text" id="post-location" name="location" placeholder="City or address" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-btn">List Vehicle</button>
                            <button type="button" id="cancel-post" class="cancel-btn">Cancel</button>
                        </div>
                    </form>
                </div>
            </section>
            <div class="recent-orders">
                <h2>Posted Vehicles</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Price/Day</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="car-rentals-table">
                        <!-- Data will be dynamically loaded here -->
                    </tbody>
                </table>
            </div>
        </main>
        <!-- End of Main Content -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const createPostBtn = document.getElementById('create-post-btn');
            const postFormContainer = document.getElementById('post-form-container');
            const cancelPostBtn = document.getElementById('cancel-post');
            const postForm = document.getElementById('post-form');

            // Show popup
            createPostBtn.addEventListener('click', function () {
                postFormContainer.style.display = 'block';
            });

            // Hide popup
            cancelPostBtn.addEventListener('click', function () {
                postFormContainer.style.display = 'none';
            });

            // Handle form submission
            postForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(postForm);

                fetch('../../controllers/CarRentalController.php?action=create', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Car rental post created successfully!');
                        postFormContainer.style.display = 'none';
                        location.reload(); // Reload to update the table
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error creating post:', error);
                    alert('An error occurred while creating the post. Please check the logs for more details.');
                });
            });

            // Fetch and display car rentals
            fetch('../../controllers/CarRentalController.php?action=fetch')
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('car-rentals-table');
                    tableBody.innerHTML = '';

                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="4">No car rentals found.</td></tr>';
                        return;
                    }

                    data.forEach(rental => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${rental.title || 'N/A'}</td>
                            <td>${rental.description.substring(0, 50)}...</td>
                            <td>$${rental.daily_rate}</td>
                            <td>
                                <button class="edit-btn" data-id="${rental.rental_id}">Edit</button>
                                <button class="delete-btn" data-id="${rental.rental_id}">Delete</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });

                    // Add event listeners for edit and delete buttons
                    document.querySelectorAll('.delete-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const rentalId = this.dataset.id;
                            if (confirm('Are you sure you want to delete this rental?')) {
                                fetch(`../../controllers/CarRentalController.php?action=delete&rental_id=${rentalId}`, { method: 'POST' })
                                    .then(response => response.json())
                                    .then(result => {
                                        if (result.success) {
                                            alert('Rental deleted successfully!');
                                            location.reload();
                                        } else {
                                            alert('Error: ' + result.message);
                                        }
                                    });
                            }
                        });
                    });

                    document.querySelectorAll('.edit-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const rentalId = this.dataset.id;
                            window.location.href = `edit-car-rental.php?rental_id=${rentalId}`;
                        });
                    });
                })
                .catch(error => console.error('Error fetching car rentals:', error));
        });
    </script>
</body>

</html>
