<?php
// Database connection
$dsn = 'mysql:host=localhost;dbname=clyptor;charset=utf8mb4';
$pdo = new PDO($dsn, 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Fetch all users
$users = $pdo->query("SELECT user_id, username, email, is_verified FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Users</title>
    <style>
        /* Add custom styles */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
        }
        .styled-table th, .styled-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .styled-table th {
            background-color: #f4f4f4;
        }
        .actions button {
            margin-right: 5px;
        }
        .form-container {
            margin: 20px 0;
        }
        .form-container input, .form-container button {
            padding: 10px;
            margin-right: 10px;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            text-align: center;
        }
        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-modal:hover,
        .close-modal:focus {
            color: black;
            text-decoration: none;
        }

        /* Add styles for the Create User modal */
        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .modal-content form input, .modal-content form button {
            padding: 10px;
            font-size: 16px;
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
                <a href="users.php" class="active">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>Users</h3>
                </a>
                <div class="submenu">
                    <a href="admincar.php">car-rent</a>
                    <a href="history2.php">covoiturage</a>
                    <a href="history3.php">home-rent</a>
                    <a href="history4.php">deliver-package</a>
                    <a href="history4.php">deliver-package</a>
                </div>
                <a href="ticket.php">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Tickets</h3>
                    <span class="message-count">27</span>
                </a>
                <a href="logout.php">
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
            <div class="header">
                <h1>Users</h1>
                <div class="actions">
                    <button class="btn btn-primary" id="create-user-btn">
                        <span class="material-icons-sharp">add</span> Create User
                    </button>
                </div>
            </div>

            <!-- Add User Form -->
            <div class="form-container">
                <form method="POST" action="add_user.php">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>

            <!-- Users Table -->
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Verified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['is_verified'] ? 'Yes' : 'No' ?></td>
                            <td>
                                <form method="POST" action="edit_user.php" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                    <button type="submit" class="btn btn-warning">Edit</button>
                                </form>
                                <form method="POST" action="delete_user.php" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Modal Popup -->
            <div id="popup-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <p id="modal-message">Are you sure you want to perform this action?</p>
                    <button id="confirm-btn" class="btn btn-primary">Confirm</button>
                    <button id="cancel-btn" class="btn btn-secondary">Cancel</button>
                </div>
            </div>

            <!-- Create User Modal -->
            <div id="create-user-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2>Create User</h2>
                    <form method="POST" action="add_user.php">
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </form>
                </div>
            </div>
        </main>
        <!-- End of Main Content -->

    </div>

    <script>
        // JavaScript for handling the modal popup
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('popup-modal');
            const closeModal = document.querySelector('.close-modal');
            const confirmBtn = document.getElementById('confirm-btn');
            const cancelBtn = document.getElementById('cancel-btn');
            let formToSubmit = null;

            // Open modal on button click
            document.querySelectorAll('form button').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    formToSubmit = button.closest('form');
                    document.getElementById('modal-message').textContent = `Are you sure you want to ${button.textContent.trim()} this user?`;
                    modal.style.display = 'block';
                });
            });

            // Close modal
            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            cancelBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            // Confirm action
            confirmBtn.addEventListener('click', () => {
                if (formToSubmit) {
                    formToSubmit.submit();
                }
            });

            // Close modal when clicking outside of it
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // JavaScript for handling the Create User modal
            const createUserBtn = document.getElementById('create-user-btn');
            const createUserModal = document.getElementById('create-user-modal');
            const closeModalButtons = document.querySelectorAll('.close-modal');

            // Open Create User modal
            createUserBtn.addEventListener('click', () => {
                createUserModal.style.display = 'block';
            });

            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    createUserModal.style.display = 'none';
                    document.getElementById('popup-modal').style.display = 'none';
                });
            });

            // Close modal when clicking outside of it
            window.addEventListener('click', (e) => {
                if (e.target === createUserModal) {
                    createUserModal.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>