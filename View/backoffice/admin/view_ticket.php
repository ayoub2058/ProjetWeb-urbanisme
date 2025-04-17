<?php
// Include the necessary files
require_once '../../../config.php';
require_once MODEL_PATH . '/Database.php';
require_once MODEL_PATH . '/Message.php';
require_once MODEL_PATH . '/Response.php';

// Check if the message ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: tickets.php");
    exit;
}

$message_id = (int)$_GET['id'];

// Initialize the database connection
$database = new Database();
$db = $database->getConnection();

// Create message and response objects
$message = new Message($db);
$response = new Response($db);

// Get the message details
$message_details = $message->readOne($message_id);
if (!$message_details) {
    header("Location: tickets.php");
    exit;
}

// Get existing responses for this message
$responses = $response->getResponsesForMessage($message_id);

// Handle forms
$success_message = '';
$error_message = '';

// Handle adding a new response
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_response'])) {
    $response_text = $_POST['response_text'] ?? '';
    
    if (empty($response_text)) {
        $error_message = "Le texte de la réponse ne peut pas être vide.";
    } else {
        $response->message_id = $message_id;
        $response->admin_name = "Admin"; // In a real app, get this from the session
        $response->response_text = $response_text;
        
        if ($response->create()) {
            // Update message status to "Traité"
            $message->id = $message_id;
            $message->status = "Traité";
            $message->updateStatus();
            
            $success_message = "Réponse ajoutée avec succès.";
            // Refresh responses list
            $responses = $response->getResponsesForMessage($message_id);
        } else {
            $error_message = "Échec de l'ajout de la réponse.";
        }
    }
}

// Handle editing a response
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_response'])) {
    $response_id = $_POST['response_id'] ?? 0;
    $response_text = $_POST['response_text'] ?? '';
    
    if (empty($response_text) || empty($response_id)) {
        $error_message = "Le texte de la réponse ne peut pas être vide.";
    } else {
        $response->id = $response_id;
        $response->response_text = $response_text;
        
        if ($response->update()) {
            $success_message = "Réponse mise à jour avec succès.";
            // Refresh responses list
            $responses = $response->getResponsesForMessage($message_id);
        } else {
            $error_message = "Échec de la mise à jour de la réponse.";
        }
    }
}

// Handle deleting a response
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_response'])) {
    $response_id = $_POST['response_id'] ?? 0;
    
    if (empty($response_id)) {
        $error_message = "ID de réponse invalide.";
    } else {
        $response->id = $response_id;
        
        if ($response->delete()) {
            $success_message = "Réponse supprimée avec succès.";
            // Refresh responses list
            $responses = $response->getResponsesForMessage($message_id);
        } else {
            $error_message = "Échec de la suppression de la réponse.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Détails du message - Clyptor</title>
    <style>
        .message-details {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .message-header h2 {
            margin: 0;
            color: #333;
        }
        .message-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .message-meta div {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .message-meta .material-icons-sharp {
            font-size: 18px;
            color: #777;
        }
        .message-content {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            white-space: pre-wrap;
        }
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
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
        .responses-section {
            margin-top: 30px;
        }
        .response-card {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .response-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .response-meta {
            font-size: 0.9rem;
            color: #777;
        }
        .response-content {
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            margin-bottom: 10px;
            white-space: pre-wrap;
        }
        .response-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .response-actions button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-edit {
            background-color: #2196F3;
            color: white;
        }
        .btn-delete {
            background-color: #F44336;
            color: white;
        }
        .response-form {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .response-form h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 100px;
            font-family: inherit;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-cancel {
            background-color: #ccc;
            color: #333;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
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
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-modal:hover {
            color: #333;
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
            <div class="header-actions">
                <a href="tickets.php" class="btn-cancel">
                    <span class="material-icons-sharp">arrow_back</span> Retour à la liste
                </a>
            </div>
            
            <h1>Détails du message</h1>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="message-details">
                <div class="message-header">
                    <h2><?php echo htmlspecialchars($message_details['subject']); ?></h2>
                    <span class="status <?php echo strtolower($message_details['status']); ?>">
                        <?php echo htmlspecialchars($message_details['status']); ?>
                    </span>
                </div>
                
                <div class="message-meta">
                    <div>
                        <span class="material-icons-sharp">person</span>
                        <span><?php echo htmlspecialchars($message_details['name']); ?></span>
                    </div>
                    <div>
                        <span class="material-icons-sharp">email</span>
                        <span><?php echo htmlspecialchars($message_details['email']); ?></span>
                    </div>
                    <div>
                        <span class="material-icons-sharp">calendar_today</span>
                        <span><?php echo date('d/m/Y H:i', strtotime($message_details['created'])); ?></span>
                    </div>
                </div>
                
                <div class="message-content">
                    <?php echo nl2br(htmlspecialchars($message_details['message'])); ?>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="message_id" value="<?php echo $message_id; ?>">
                    <div class="form-actions">
                        <button type="submit" name="status" value="Lu" class="btn-primary">
                            <span class="material-icons-sharp">visibility</span> Marquer comme lu
                        </button>
                        <button type="submit" name="status" value="Traité" class="btn-primary">
                            <span class="material-icons-sharp">check_circle</span> Marquer comme traité
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="responses-section">
                <h2>Réponses (<?php echo count($responses); ?>)</h2>
                
                <?php if (!empty($responses)): ?>
                    <?php foreach ($responses as $resp): ?>
                        <div class="response-card">
                            <div class="response-header">
                                <div>
                                    <strong><?php echo htmlspecialchars($resp['admin_name']); ?></strong>
                                </div>
                                <div class="response-meta">
                                    <?php echo date('d/m/Y H:i', strtotime($resp['created'])); ?>
                                </div>
                            </div>
                            <div class="response-content">
                                <?php echo nl2br(htmlspecialchars($resp['response_text'])); ?>
                            </div>
                            <div class="response-actions">
                                <button class="btn-edit" onclick="openEditModal(<?php echo $resp['id']; ?>, '<?php echo addslashes(htmlspecialchars($resp['response_text'])); ?>')">
                                    <span class="material-icons-sharp">edit</span> Modifier
                                </button>
                                <button class="btn-delete" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette réponse?')) { document.getElementById('delete-form-<?php echo $resp['id']; ?>').submit(); }">
                                    <span class="material-icons-sharp">delete</span> Supprimer
                                </button>
                                <form id="delete-form-<?php echo $resp['id']; ?>" method="POST" action="" style="display: none;">
                                    <input type="hidden" name="response_id" value="<?php echo $resp['id']; ?>">
                                    <input type="hidden" name="delete_response" value="1">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune réponse pour ce message.</p>
                <?php endif; ?>
                
                <div class="response-form">
                    <h3>Ajouter une réponse</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="response-text">Votre réponse :</label>
                            <textarea id="response-text" name="response_text" required></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="add_response" class="btn-primary">Envoyer la réponse</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Edit Response Modal -->
            <div id="edit-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeEditModal()">&times;</span>
                    <h3>Modifier la réponse</h3>
                    <form method="POST" action="">
                        <input type="hidden" id="edit-response-id" name="response_id" value="">
                        <div class="form-group">
                            <label for="edit-response-text">Votre réponse :</label>
                            <textarea id="edit-response-text" name="response_text" required></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="closeEditModal()">Annuler</button>
                            <button type="submit" name="edit_response" class="btn-primary">Mettre à jour</button>
                        </div>
                    </form>
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

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-btn');
        const sidebar = document.querySelector('aside');
        const editModal = document.getElementById('edit-modal');
        const editResponseId = document.getElementById('edit-response-id');
        const editResponseText = document.getElementById('edit-response-text');

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

        // Modal functions
        function openEditModal(id, text) {
            editResponseId.value = id;
            editResponseText.value = text;
            editModal.style.display = 'block';
        }

        function closeEditModal() {
            editModal.style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.addEventListener('click', (event) => {
            if (event.target === editModal) {
                closeEditModal();
            }
        });
    </script>
</body>

</html> 