<?php
require_once '../../models/PackageDelivery.php';
require_once '../../controllers/PackageDeliveryController.php';

use Models\PackageDelivery;
use Controllers\PackageDeliveryController;

session_start();

// Check if the user is an admin
//if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
//    die("Access denied. Admins only.");
//}

// Database connection
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=clyptor', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize model and controller
$model = new PackageDelivery($db);
$controller = new PackageDeliveryController($model);

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $deliveryId = $_POST['delivery_id'] ?? null;

    if ($action === 'edit' && $deliveryId) {
        $data = [
            'delivery_id' => $deliveryId,
            'package_description' => $_POST['package_description'] ?? '',
            'package_weight' => $_POST['package_weight'] ?? '',
            'package_dimensions' => $_POST['package_dimensions'] ?? '',
            'estimated_value' => $_POST['estimated_value'] ?? '',
            'delivery_deadline' => $_POST['delivery_deadline'] ?? '',
            'proposed_price' => $_POST['proposed_price'] ?? '',
        ];
        $response = $controller->editPost($data);
        $message = $response['message'];
    } elseif ($action === 'delete' && $deliveryId) {
        $response = $controller->deletePost($deliveryId);
        $message = $response['message'];
    }
}

// Fetch all package deliveries
$packages = $controller->getAllPackages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Package Deliveries</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* General Main Section Styling */
        main {
            margin-left: 250px; /* Adjust for sidebar width */
            padding: 20px;
            background-color: #f4f4f9;
            min-height: 100vh;
        }

        main h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #e7f3e7;
            color: #2d7a2d;
            font-weight: bold;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table thead {
            background-color: #007bff;
            color: #fff;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            font-weight: bold;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table td:last-child {
            display: flex;
            gap: 10px;
        }

        /* Button Styling */
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        button.delete-btn {
            background-color: #dc3545;
            color: #fff;
        }

        button.delete-btn:hover {
            background-color: #a71d2a;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            main {
                margin-left: 0;
                padding: 10px;
            }

            table th, table td {
                padding: 10px;
                font-size: 0.9rem;
            }

            button {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <img src="images/logo.png">
                    <h2>Cly<span class="danger">Ptor</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>
            <div class="sidebar">
                <a href="admin.php">
                    <span class="material-icons-sharp">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="users.php">
                    <span class="material-icons-sharp">person_outline</span>
                    <h3>Users</h3>
                </a>
                <a href="admincar.php">
                    <span class="material-icons-sharp">receipt_long</span>
                    <h3>Car Rentals</h3>
                </a>
                <a href="adminpackage.php" class="active">
                    <span class="material-icons-sharp">local_shipping</span>
                    <h3>Package Deliveries</h3>
                </a>
                <a href="admincovoiturage.php">
                    <span class="material-icons-sharp">directions_car</span>
                    <h3>Covoiturage</h3>
                </a>
                <a href="adminhome.php">
                    <span class="material-icons-sharp">home</span>
                    <h3>Home Rentals</h3>
                </a>
                <a href="ticket.php">
                    <span class="material-icons-sharp">mail_outline</span>
                    <h3>Tickets</h3>
                </a>
                <a href="logout.php">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar -->

        <!-- Main Content -->
        <main>
            <h1>Package Deliveries</h1>
            <?php if ($message): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sender</th>
                        <th>Pickup Address</th>
                        <th>Delivery Address</th>
                        <th>Description</th>
                        <th>Weight</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $package): ?>
                        <tr>
                            <td><?= htmlspecialchars($package['delivery_id']) ?></td>
                            <td><?= htmlspecialchars($package['sender_id']) ?></td>
                            <td><?= htmlspecialchars($package['pickup_address_id']) ?></td>
                            <td><?= htmlspecialchars($package['delivery_address_id']) ?></td>
                            <td><?= htmlspecialchars($package['package_description']) ?></td>
                            <td><?= htmlspecialchars($package['package_weight']) ?> kg</td>
                            <td>$<?= htmlspecialchars($package['proposed_price']) ?></td>
                            <td><?= htmlspecialchars($package['status']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="delivery_id" value="<?= $package['delivery_id'] ?>">
                                    <input type="text" name="package_description" value="<?= htmlspecialchars($package['package_description']) ?>" placeholder="Description">
                                    <input type="text" name="package_weight" value="<?= htmlspecialchars($package['package_weight']) ?>" placeholder="Weight">
                                    <input type="text" name="proposed_price" value="<?= htmlspecialchars($package['proposed_price']) ?>" placeholder="Price">
                                    <button type="submit">Edit</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="delivery_id" value="<?= $package['delivery_id'] ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
