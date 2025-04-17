<?php
// Include the necessary files
require_once '../../../config.php';
require_once MODEL_PATH . '/Database.php';
require_once MODEL_PATH . '/Message.php';
require_once MODEL_PATH . '/Response.php';

// Initialize the database connection
$database = new Database();
$db = $database->getConnection();

// Create message and response objects
$message = new Message($db);
$response = new Response($db);

// Get all messages
$stmt = $message->read();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count all messages
$total_messages = count($messages);

// Process status change if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id']) && isset($_POST['status'])) {
    $message->id = $_POST['message_id'];
    $message->status = $_POST['status'];
    
    if ($message->updateStatus()) {
        // Redirect to refresh the page
        header("Location: tickets.php?success=1");
        exit;
    } else {
        $error_message = "Échec de la mise à jour du statut.";
    }
}

// Check for success message
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Statut mis à jour avec succès.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Messages de contact - Clyptor</title>
    <style>
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        .status.nouveau {
            background: #FFD700;
            color: #333;
        }
        .status.lu {
            background: #4CAF50;
            color: white;
        }
        .status.en-cours {
            background: #2196F3;
            color: white;
        }
        .status.traité {
            background: #9E9E9E;
            color: white;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #DFF2BF;
            color: #4F8A10;
        }
        .alert-danger {
            background-color: #FFBABA;
            color: #D8000C;
        }
        .message-content {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .action-buttons button, .action-buttons a {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-view {
            background-color: #673AB7;
            color: white;
        }
        .response-count {
            background-color: #ff9800;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            margin-left: 5px;
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
                <a href="admin.html">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="users.html">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>Users</h3>
                </a>
                <a href="history.html">
                    <span class="material-icons-sharp">
                        receipt_long
                    </span>
                    <h3>History</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        insights
                    </span>
                    <h3>Analytics</h3>
                </a>
                <a href="tickets.php" class="active">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Messages</h3>
                    <span class="message-count"><?php echo $total_messages; ?></span>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        inventory
                    </span>
                    <h3>Trajet</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        report_gmailerrorred
                    </span>
                    <h3>Reports</h3>
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
            <h1>Messages de contact</h1>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="tickets">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Sujet</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($messages) > 0): ?>
                            <?php foreach ($messages as $msg): 
                                // Get response count for this message
                                $response_count = count($response->getResponsesForMessage($msg['id']));
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                    <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                    <td class="message-content"><?php echo htmlspecialchars($msg['message']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($msg['created'])); ?></td>
                                    <td>
                                        <span class="status <?php echo strtolower($msg['status']); ?>">
                                            <?php echo htmlspecialchars($msg['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view_ticket.php?id=<?php echo $msg['id']; ?>" class="btn-view">
                                                <span class="material-icons-sharp">visibility</span> Voir
                                                <?php if ($response_count > 0): ?>
                                                <span class="response-count"><?php echo $response_count; ?></span>
                                                <?php endif; ?>
                                            </a>
                                            <form method="POST" action="">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                <input type="hidden" name="status" value="Lu">
                                                <button type="submit" style="background-color: #2196F3; color: white;">
                                                    <span class="material-icons-sharp">mark_email_read</span> Lu
                                                </button>
                                            </form>
                                            <form method="POST" action="">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                <input type="hidden" name="status" value="Traité">
                                                <button type="submit" style="background-color: #4CAF50; color: white;">
                                                    <span class="material-icons-sharp">check_circle</span> Traité
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Aucun message de contact trouvé.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-btn');
        const sidebar = document.querySelector('aside');

        menuBtn.addEventListener('click', () => {
            sidebar.style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
            sidebar.style.display = 'none';
        });

        // Dark mode toggle
        const darkModeToggle = document.querySelector('.dark-mode');
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode-variables');
            darkModeToggle.querySelector('span:nth-child(1)').classList.toggle('active');
            darkModeToggle.querySelector('span:nth-child(2)').classList.toggle('active');
        });
    </script>
</body>

</html> 