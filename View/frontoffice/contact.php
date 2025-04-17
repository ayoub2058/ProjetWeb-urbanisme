<?php
// Inclure le fichier de configuration
require_once '../../config.php';

// Vérifier si le formulaire a été soumis
$success_message = '';
$error_message = '';

// Vérifier si l'utilisateur est connecté
$is_user_logged_in = isset($_SESSION['user_id']);
$user_email = $is_user_logged_in && isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Initialiser la base de données et le modèle Message
require_once MODEL_PATH . '/Database.php';
require_once MODEL_PATH . '/Message.php';
require_once MODEL_PATH . '/Response.php';
$database = new Database();
$db = $database->getConnection();
$contact_message = new Message($db);
$response = new Response($db);

// Traiter les actions sur les tickets
if ($is_user_logged_in && isset($_GET['action'])) {
    $message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    // Vérifier que le message appartient à l'utilisateur
    if ($message_id > 0) {
        $contact_message->id = $message_id;
        
        if ($_GET['action'] === 'delete' && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
            if ($contact_message->delete()) {
                $success_message = "Votre message a été supprimé avec succès.";
            } else {
                $error_message = "Erreur lors de la suppression du message.";
            }
        } elseif ($_GET['action'] === 'edit' && isset($_POST['update'])) {
            $contact_message->subject = $_POST['subject'];
            $contact_message->message = $_POST['message'];
            
            if ($contact_message->update()) {
                $success_message = "Votre message a été mis à jour avec succès.";
            } else {
                $error_message = "Erreur lors de la mise à jour du message.";
            }
        }
    }
}

// Récupérer les messages de l'utilisateur connecté
$user_messages = [];
if ($is_user_logged_in) {
    $user_messages = $contact_message->getUserMessages($user_email);
}

// Soumettre un nouveau message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update'])) {
    // Vérifier si la table existe
    try {
        $tableExistsQuery = "SHOW TABLES LIKE 'contact_messages'";
        $stmt = $db->prepare($tableExistsQuery);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            // La table n'existe pas, créons-la
            $createTableQuery = "CREATE TABLE IF NOT EXISTS contact_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                subject VARCHAR(200) NOT NULL,
                message TEXT NOT NULL,
                status VARCHAR(20) DEFAULT 'Nouveau',
                created DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;";
            $db->exec($createTableQuery);
        }
    } catch(PDOException $e) {
        $error_message = "Erreur de base de données: " . $e->getMessage();
    }
    
    // Récupérer les données du formulaire
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    
    // Définir les valeurs du message
    $contact_message->name = $name;
    $contact_message->email = $email;
    $contact_message->subject = $subject;
    $contact_message->message = $message;
    $contact_message->status = "Nouveau";
    
    // Enregistrer le message
    if (empty($error_message)) {
        if ($contact_message->create()) {
            $success_message = "Votre message a été envoyé avec succès!";
            
            // Rafraîchir la liste des messages si l'utilisateur est connecté
            if ($is_user_logged_in) {
                $user_messages = $contact_message->getUserMessages($user_email);
            }
        } else {
            $error_message = "Une erreur est survenue lors de l'envoi du message.";
        }
    }
}

// Inclure l'en-tête
$pageTitle = "Contact";
// Remove the header include and replace with direct HTML
// include_once "../includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clyptor - Contact</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/hf.css">
    <link rel="stylesheet" href="css/animations.css">
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
                <li><a href="index.html">Home</a></li>
                <li><a href="covoiturage.html">Carpooling</a></li>
                <li><a href="home-rent.html">Home Rent</a></li>
                <li><a href="car-rent.html">Car Rent</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
        
        <div class="auth-buttons">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="?page=user&action=dashboard" class="btn btn-outline"><?php echo $_SESSION['username']; ?></a>
                <a href="?page=user&action=logout" class="btn btn-primary">Logout</a>
            <?php else: ?>
                <a href="login.html" class="btn btn-outline">Login</a>
                <a href="login.html" class="btn btn-primary">Register</a>
            <?php endif; ?>
        </div>
        
        <button class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </header>

<!-- Include the dark mode and Three.js script -->
<script src="js/dark-contact.js"></script>

<main class="contact-main">
    <section class="contact-hero">
        <h1>Contact Us</h1>
        <p>Have questions or feedback? We'd love to hear from you!</p>
    </section>

    <!-- Remove the canvas container and add Spline iframe -->
    <div class="spline-container">
        <iframe src='https://my.spline.design/rocket-zCKAyLXPTXimVRoIOnd2buNl/' frameborder='0' width='100%' height='100%'></iframe>
    </div>

    <section class="contact-container">
        <div class="contact-form-container">
            <h2>Send Us a Message</h2>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])): 
                // Afficher le formulaire d'édition
                $message_id = intval($_GET['id']);
                $message_details = $contact_message->readOne($message_id);
                if ($message_details):
            ?>
                <form id="edit-form" class="contact-form" method="POST" action="?action=edit&id=<?php echo $message_id; ?>">
                    <div class="form-group">
                        <label for="contact-name">Your Name</label>
                        <input type="text" id="contact-name" name="name" value="<?php echo htmlspecialchars($message_details['name']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Your Email</label>
                        <input type="email" id="contact-email" name="email" value="<?php echo htmlspecialchars($message_details['email']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="contact-subject">Subject</label>
                        <input type="text" id="contact-subject" name="subject" value="<?php echo htmlspecialchars($message_details['subject']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-message">Message</label>
                        <textarea id="contact-message" name="message" rows="5" required><?php echo htmlspecialchars($message_details['message']); ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="update" class="submit-btn">Update Message</button>
                        <a href="contact.php" class="cancel-btn">Cancel</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">Message not found.</div>
            <?php endif; ?>
            <?php else: ?>
                <!-- Formulaire standard de contact -->
                <form id="contact-form" class="contact-form" method="POST" action="">
                    <div class="form-group">
                        <label for="contact-name">Your Name</label>
                        <input type="text" id="contact-name" name="name" value="<?php echo $is_user_logged_in ? htmlspecialchars($_SESSION['username']) : ''; ?>" required <?php echo $is_user_logged_in ? 'readonly' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Your Email</label>
                        <input type="email" id="contact-email" name="email" value="<?php echo $user_email; ?>" required <?php echo $is_user_logged_in ? 'readonly' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label for="contact-subject">Subject</label>
                        <input type="text" id="contact-subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-message">Message</label>
                        <textarea id="contact-message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="contact-info-container">
            <h2>Our Information</h2>
            <div class="contact-info">
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>TUNIS, BELVEDERE<br>, BUREAU N°1</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <p>+216 52 180 466</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <p>info@clyptor.tn</p>
                </div>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </section>

    <?php if ($is_user_logged_in): ?>
    <!-- Ticket History Section - Enhanced from My Messages section -->
    <section class="ticket-history-section">
        <h2>Ticket History</h2>
        
        <?php if (!empty($user_messages)): ?>
            <!-- Ticket filters and controls -->
            <div class="ticket-controls">
                <div class="ticket-filters">
                    <div class="filter-group">
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" class="filter-select">
                            <option value="all">All Statuses</option>
                            <option value="nouveau">New</option>
                            <option value="lu">Read</option>
                            <option value="traité">Processed</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="date-filter">Date:</label>
                        <select id="date-filter" class="filter-select">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                </div>
                <div class="ticket-view-options">
                    <button id="grid-view-btn" class="view-btn active"><i class="fas fa-th-large"></i></button>
                    <button id="list-view-btn" class="view-btn"><i class="fas fa-list"></i></button>
                </div>
            </div>
            
            <!-- Ticket search -->
            <div class="ticket-search">
                <input type="text" id="ticket-search-input" placeholder="Search your tickets...">
                <button id="ticket-search-btn"><i class="fas fa-search"></i></button>
            </div>
            
            <!-- Ticket stats -->
            <div class="ticket-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo count($user_messages); ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo count(array_filter($user_messages, function($m) { return $m['status'] === 'Nouveau'; })); ?></div>
                    <div class="stat-label">New</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo count(array_filter($user_messages, function($m) { return $m['status'] === 'Lu'; })); ?></div>
                    <div class="stat-label">Read</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo count(array_filter($user_messages, function($m) { return $m['status'] === 'Traité'; })); ?></div>
                    <div class="stat-label">Processed</div>
                </div>
            </div>
            
            <!-- Ticket container with enhanced UI -->
            <div id="tickets-container" class="tickets-container grid-view">
                <?php foreach ($user_messages as $msg): ?>
                <div class="ticket-card" data-status="<?php echo strtolower($msg['status']); ?>" data-date="<?php echo date('Y-m-d', strtotime($msg['created'])); ?>">
                    <div class="ticket-header">
                        <h3 title="<?php echo htmlspecialchars($msg['subject']); ?>"><?php echo htmlspecialchars($msg['subject']); ?></h3>
                        <span class="ticket-date"><?php echo date('d/m/Y H:i', strtotime($msg['created'])); ?></span>
                    </div>
                    <div class="ticket-content">
                        <p><?php echo htmlspecialchars($msg['message']); ?></p>
                    </div>
                    <div class="ticket-footer">
                        <span class="ticket-status <?php echo strtolower($msg['status']); ?>">
                            <?php echo htmlspecialchars($msg['status']); ?>
                        </span>
                        <div class="ticket-actions">
                            <a href="?action=edit&id=<?php echo $msg['id']; ?>" class="edit-btn" title="Edit this ticket"><i class="fas fa-edit"></i> Edit</a>
                            <a href="?action=delete&id=<?php echo $msg['id']; ?>&confirm=yes" class="delete-btn" title="Delete this ticket" onclick="return confirm('Are you sure you want to delete this message?')"><i class="fas fa-trash-alt"></i> Delete</a>
                            <button class="view-btn" title="View details" onclick="viewTicketDetails(<?php echo $msg['id']; ?>)"><i class="fas fa-eye"></i> View</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Ticket detail modal -->
            <div id="ticket-detail-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2 id="modal-ticket-subject"></h2>
                    <div class="modal-ticket-info">
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span id="modal-ticket-status" class="ticket-status"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Created:</span>
                            <span id="modal-ticket-date"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Ticket ID:</span>
                            <span id="modal-ticket-id"></span>
                        </div>
                    </div>
                    <div class="modal-ticket-message">
                        <h3>Message:</h3>
                        <p id="modal-ticket-message"></p>
                    </div>
                    
                    <!-- Admin responses section -->
                    <div class="modal-responses" id="modal-responses">
                        <h3>Responses from Admin:</h3>
                        <div id="modal-responses-container"></div>
                    </div>
                    
                    <div class="modal-action-buttons">
                        <a id="modal-edit-link" href="#" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
                        <a id="modal-delete-link" href="#" class="delete-btn" onclick="return confirm('Are you sure you want to delete this message?')"><i class="fas fa-trash-alt"></i> Delete</a>
                        <button class="close-btn">Close</button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="no-tickets">You haven't sent any messages yet. Use the contact form above to create your first ticket.</p>
        <?php endif; ?>
    </section>

    <!-- Add ticket history styling -->
    <style>
    /* Dark Mode Styles for Contact Page */
    body {
        background-color: #121212;
        color: #e0e0e0;
    }
    
    .contact-main {
        background-color: #121212;
    }
    
    .contact-hero {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        padding: 60px 20px;
        text-align: center;
        color: #ffffff;
        border-radius: 8px;
        margin-bottom: 40px;
    }
    
    .contact-hero h1 {
        margin-bottom: 20px;
        font-size: 2.5rem;
        color:rgb(132, 0, 255);
    }
    
    .contact-hero p {
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto;
        color: #b3b3b3;
    }
    
    .contact-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .contact-form-container {
        flex: 1 1 600px;
        padding: 30px;
        background-color: #1e1e1e;
        border-radius: 8px;
        box-shadow: 0 8px 30px rgba(0, 255, 255, 0.1);
    }
    
    .contact-info-container {
        flex: 1 1 300px;
        padding: 30px;
        background-color: #1e1e1e;
        border-radius: 8px;
        box-shadow: 0 8px 30px rgba(0, 255, 255, 0.1);
    }
    
    .contact-form-container h2,
    .contact-info-container h2 {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgb(132, 0, 255);
        color:rgb(132, 0, 255);
    }
    
    .contact-form .form-group {
        margin-bottom: 20px;
    }
    
    .contact-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #b3b3b3;
    }
    
    .contact-form input,
    .contact-form textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #333;
        border-radius: 6px;
        background-color: #2d2d2d;
        color: #e0e0e0;
        font-size: 16px;
        transition: all 0.3s;
    }
    
    .contact-form input:focus,
    .contact-form textarea:focus {
        border-color:rgb(132, 0, 255);
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 255, 255, 0.3);
    }
    
    .contact-form button {
        display: inline-block;
        padding: 12px 25px;
        background: linear-gradient(135deg, rgb(132, 0, 255), rgb(132, 0, 255));
        color: #121212;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .contact-form button:hover {
        background: linear-gradient(135deg, #0c9db5, #00ffff);
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 255, 255, 0.3);
    }
    
    .contact-info {
        margin-bottom: 30px;
    }
    
    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    
    .info-item i {
        min-width: 40px;
        height: 40px;
        background-color: rgba(0, 255, 255, 0.1);
        color: #rgb(132, 0, 255);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 15px;
        font-size: 18px;
    }
    
    .info-item p {
        margin: 0;
        color: #b3b3b3;
    }
    
    .social-links {
        display: flex;
        gap: 15px;
    }
    
    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: rgba(0, 255, 255, 0.1);
        color: #rgb(132, 0, 255);
        border-radius: 50%;
        transition: all 0.3s;
    }
    
    .social-links a:hover {
        background-color: rgb(132, 0, 255);
        color: #121212;
        transform: translateY(-3px);
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 6px;
    }
    
    .alert-success {
        background-color: rgba(0, 255, 128, 0.2);
        color: rgb(132, 0, 255);
        border: 1px solid rgba(46, 204, 113, 0.5);
    }
    
    .alert-danger {
        background-color: rgba(255, 0, 0, 0.2);
        color: #ff6b6b;
        border: 1px solid rgba(255, 107, 107, 0.5);
    }
    
    /* Ticket History Section Styles */
    .ticket-history-section {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
    }
    
    .ticket-history-section h2 {
        color:rgb(132, 0, 255);
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgb(132, 0, 255);
    }
    
    .ticket-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .ticket-filters {
        display: flex;
        gap: 15px;
    }
    
    .filter-group {
        display: flex;
        align-items: center;
    }
    
    .filter-group label {
        margin-right: 10px;
        color: #b3b3b3;
    }
    
    .filter-select {
        padding: 8px 12px;
        background-color: #2d2d2d;
        border: 1px solid #333;
        border-radius: 4px;
        color: #e0e0e0;
    }
    
    .ticket-view-options {
        display: flex;
        gap: 10px;
    }
    
    .view-btn {
        width: 40px;
        height: 40px;
        background-color: #2d2d2d;
        border: 1px solid #333;
        border-radius: 4px;
        color: #b3b3b3;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .view-btn.active, .view-btn:hover {
        background-color: rgba(0, 255, 255, 0.1);
        color: rgb(132, 0, 255);
    }
    
    .ticket-search {
        display: flex;
        margin-bottom: 20px;
    }
    
    .ticket-search input {
        flex: 1;
        padding: 10px 15px;
        background-color: #2d2d2d;
        border: 1px solid #333;
        border-right: none;
        border-radius: 6px 0 0 6px;
        color: #e0e0e0;
    }
    
    .ticket-search button {
        padding: 10px 15px;
        background-color: #2d2d2d;
        border: 1px solid #333;
        border-left: none;
        border-radius: 0 6px 6px 0;
        color: #b3b3b3;
        cursor: pointer;
    }
    
    .ticket-search button:hover {
        color: #00ffff;
    }
    
    #canvas-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        opacity: 0.2;
    }
    
    @media (max-width: 768px) {
        .contact-container {
            flex-direction: column;
        }
        
        .ticket-controls {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }
        
        .ticket-filters {
            flex-direction: column;
            gap: 10px;
        }
    }
    
    /* Modal styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        overflow: auto;
    }
    
    .modal-content {
        position: relative;
        background-color: #000000;
        color: #ffffff;
        margin: 50px auto;
        width: 80%;
        max-width: 800px;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 30px rgba(0, 255, 255, 0.3);
        border: 1px solid #008000;
    }
    
    .close-modal {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        color: #ffffff;
        cursor: pointer;
    }
    
    .close-modal:hover {
        color: #00ff00;
    }
    
    .modal-ticket-info {
        background-color: #0a0a0a;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 3px solid #00ff00;
    }
    
    .info-row {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    
    .info-label {
        font-weight: bold;
        margin-right: 10px;
        width: 80px;
        color: #00ff00;
    }
    
    .modal-ticket-message {
        background-color: #111111;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 3px solid #008000;
    }
    
    .modal-ticket-message h3 {
        color: #00ff00;
        margin-top: 0;
        margin-bottom: 10px;
    }
    
    .modal-responses {
        background-color: #111111;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 3px solid #008000;
    }
    
    .modal-responses h3 {
        color: #00ff00;
        margin-top: 0;
        margin-bottom: 15px;
    }
    
    .modal-action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }
    
    .modal-action-buttons .edit-btn,
    .modal-action-buttons .delete-btn {
        padding: 8px 15px;
    }
    
    .modal-action-buttons .close-btn {
        background-color: #008000;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }
    
    .modal-action-buttons .close-btn:hover {
        background-color: #00aa00;
    }
    
    #modal-ticket-subject {
        color: #ffffff;
        border-bottom: 2px solid #00ff00;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    </style>

    <script>
    // Ticket history functionality
    document.addEventListener('DOMContentLoaded', function() {
        // View toggle
        const gridViewBtn = document.getElementById('grid-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const ticketsContainer = document.getElementById('tickets-container');
        
        if (gridViewBtn && listViewBtn && ticketsContainer) {
            gridViewBtn.addEventListener('click', function() {
                ticketsContainer.classList.remove('list-view');
                ticketsContainer.classList.add('grid-view');
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
            });
            
            listViewBtn.addEventListener('click', function() {
                ticketsContainer.classList.remove('grid-view');
                ticketsContainer.classList.add('list-view');
                listViewBtn.classList.add('active');
                gridViewBtn.classList.remove('active');
            });
            
            // Filtering
            const statusFilter = document.getElementById('status-filter');
            const dateFilter = document.getElementById('date-filter');
            const searchInput = document.getElementById('ticket-search-input');
            const searchBtn = document.getElementById('ticket-search-btn');
            
            function applyFilters() {
                const statusValue = statusFilter.value;
                const dateValue = dateFilter.value;
                const searchValue = searchInput.value.toLowerCase();
                
                const tickets = document.querySelectorAll('.ticket-card');
                
                tickets.forEach(ticket => {
                    let visible = true;
                    
                    // Status filter
                    if (statusValue !== 'all' && !ticket.getAttribute('data-status').includes(statusValue)) {
                        visible = false;
                    }
                    
                    // Date filter
                    if (dateValue !== 'all') {
                        const ticketDate = new Date(ticket.getAttribute('data-date'));
                        const today = new Date();
                        const oneDay = 24 * 60 * 60 * 1000;
                        
                        if (dateValue === 'today') {
                            if (Math.round(Math.abs((ticketDate - today) / oneDay)) > 0) {
                                visible = false;
                            }
                        } else if (dateValue === 'week') {
                            if (Math.round(Math.abs((ticketDate - today) / oneDay)) > 7) {
                                visible = false;
                            }
                        } else if (dateValue === 'month') {
                            if (Math.round(Math.abs((ticketDate - today) / oneDay)) > 30) {
                                visible = false;
                            }
                        }
                    }
                    
                    // Search filter
                    if (searchValue) {
                        const subject = ticket.querySelector('h3').textContent.toLowerCase();
                        const content = ticket.querySelector('.ticket-content p').textContent.toLowerCase();
                        
                        if (!subject.includes(searchValue) && !content.includes(searchValue)) {
                            visible = false;
                        }
                    }
                    
                    // Show or hide ticket
                    if (visible) {
                        ticket.style.display = '';
                    } else {
                        ticket.style.display = 'none';
                    }
                });
            }
            
            if (statusFilter) statusFilter.addEventListener('change', applyFilters);
            if (dateFilter) dateFilter.addEventListener('change', applyFilters);
            if (searchBtn) searchBtn.addEventListener('click', applyFilters);
            if (searchInput) searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyFilters();
                }
            });
            
            // Modal functionality
            const modal = document.getElementById('ticket-detail-modal');
            const closeModal = document.querySelector('.close-modal');
            const closeBtn = document.querySelector('.close-btn');
            
            // Close modal when clicking X or Close button
            if (closeModal) {
                closeModal.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // View ticket details function
            window.viewTicketDetails = function(ticketId) {
                const tickets = document.querySelectorAll('.ticket-card');
                
                tickets.forEach(ticket => {
                    const links = ticket.querySelectorAll('a');
                    let id = null;
                    
                    // Find the ticket ID from the edit link
                    links.forEach(link => {
                        if (link.href.includes('action=edit&id=')) {
                            const match = link.href.match(/id=(\d+)/);
                            if (match && match[1]) {
                                id = match[1];
                            }
                        }
                    });
                    
                    if (id === ticketId.toString()) {
                        // Populate modal with ticket details
                        document.getElementById('modal-ticket-subject').textContent = ticket.querySelector('h3').textContent;
                        document.getElementById('modal-ticket-message').textContent = ticket.querySelector('.ticket-content p').textContent;
                        document.getElementById('modal-ticket-date').textContent = ticket.querySelector('.ticket-date').textContent;
                        
                        const status = ticket.querySelector('.ticket-status').textContent.trim();
                        document.getElementById('modal-ticket-status').textContent = status;
                        document.getElementById('modal-ticket-status').className = 'ticket-status ' + ticket.getAttribute('data-status');
                        
                        document.getElementById('modal-ticket-id').textContent = id;
                        
                        // Set action links
                        document.getElementById('modal-edit-link').href = '?action=edit&id=' + id;
                        document.getElementById('modal-delete-link').href = '?action=delete&id=' + id + '&confirm=yes';
                        
                        // Load admin responses for this ticket
                        loadAdminResponses(id);
                        
                        // Show modal
                        modal.style.display = 'block';
                    }
                });
            };
        }
    });

    // Function to load admin responses via AJAX
    function loadAdminResponses(ticketId) {
        const responsesContainer = document.getElementById('modal-responses-container');
        responsesContainer.innerHTML = '<p>Loading responses...</p>';
        
        // Create and send the AJAX request
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    try {
                        const responses = JSON.parse(this.responseText);
                        displayAdminResponses(responses);
                    } catch (e) {
                        responsesContainer.innerHTML = '<p class="no-responses">Error loading responses.</p>';
                    }
                } else {
                    responsesContainer.innerHTML = '<p class="no-responses">Error loading responses.</p>';
                }
            }
        };
        
        xhr.open('GET', 'get_responses.php?message_id=' + ticketId, true);
        xhr.send();
    }

    // Function to display the loaded admin responses
    function displayAdminResponses(responses) {
        const responsesContainer = document.getElementById('modal-responses-container');
        
        if (responses.length === 0) {
            responsesContainer.innerHTML = '<p class="no-responses">No responses yet.</p>';
            return;
        }
        
        let html = '';
        responses.forEach(resp => {
            html += `
                <div class="admin-response">
                    <div class="admin-response-header">
                        <span>${resp.admin_name}</span>
                        <span>${formatDate(resp.created)}</span>
                    </div>
                    <div class="admin-response-content">
                        ${resp.response_text}
                    </div>
                </div>
            `;
        });
        
        responsesContainer.innerHTML = html;
    }

    // Helper function to format dates
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    </script>
    <?php endif; ?>

    <!-- AI Chatbot Widget -->
    <div class="chatbot-widget" id="chatbot-widget">
        <div class="chatbot-header">
            <h3>Clyptor Assistant</h3>
            <button id="chatbot-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="chatbot-message bot">
                <div class="message-content">
                    Hello! I'm Clyptor's AI assistant. How can I help you today?
                </div>
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatbot-user-input" placeholder="Type your message...">
            <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    <button class="chatbot-toggle" id="chatbot-toggle">
        <i class="fas fa-robot"></i>
    </button>
</main>

<style>
/* Updated styling for the 3D model container */
.model-container {
    display: none;
}

.spline-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    pointer-events: none; /* This allows clicking through to elements behind */
}

.spline-container iframe {
    width: 100%;
    height: 100%;
    opacity: 0.6; /* Adjust to make the background less prominent */
}

/* Apply a semi-transparent overlay on content to make text readable over the 3D background */
.contact-main {
    position: relative;
    z-index: 1;
}

.contact-main::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.85), rgba(255, 255, 255, 0.95));
    z-index: -1;
}

/* Fix for header visibility */
header {
    position: relative;
    z-index: 10;
}

/* Enhanced contact form styling */
.contact-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.contact-form-container {
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 2.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.contact-form-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.contact-form-container h2 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    position: relative;
    padding-bottom: 10px;
}

.contact-form-container h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, #3498db, #2ecc71);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #34495e;
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    outline: none;
}

.submit-btn {
    background: linear-gradient(135deg, #3498db, #2ecc71);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    font-size: 1rem;
    margin-top: 1rem;
}

.submit-btn:hover {
    background: linear-gradient(135deg, #2980b9, #27ae60);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.cancel-btn {
    background-color: #e74c3c;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    display: block;
    text-decoration: none;
    margin-top: 1rem;
    font-size: 1rem;
}

.cancel-btn:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.form-actions {
    display: flex;
    flex-direction: column;
}

.contact-info-container {
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 2.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.contact-info-container::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(46, 204, 113, 0.1));
    border-radius: 0 0 0 100%;
    z-index: 0;
}

.contact-info-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.contact-info-container h2 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    position: relative;
    padding-bottom: 10px;
}

.contact-info-container h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, #3498db, #2ecc71);
}

.contact-info {
    margin-bottom: 2rem;
}

.info-item {
    display: flex;
    margin-bottom: 1.5rem;
    align-items: flex-start;
}

.info-item i {
    color: #3498db;
    font-size: 1.2rem;
    margin-right: 15px;
    background: rgba(52, 152, 219, 0.1);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.info-item:hover i {
    background: #3498db;
    color: white;
    transform: scale(1.1);
}

.info-item p {
    color: #34495e;
    margin: 0;
    line-height: 1.5;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-links a {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    transition: all 0.3s ease;
}

.social-links a:hover {
    transform: translateY(-5px);
    background: #3498db;
    color: white;
}

/* Alert styling */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-weight: 500;
}

.alert-success {
    background-color: rgba(46, 204, 113, 0.2);
    color: #27ae60;
    border-left: 4px solid #27ae60;
}

.alert-danger {
    background-color: rgba(231, 76, 60, 0.2);
    color: #c0392b;
    border-left: 4px solid #c0392b;
}

/* My Tickets Section */
.my-tickets-section {
    max-width: 1200px;
    margin: 3rem auto;
    padding: 0 2rem;
    position: relative;
    z-index: 1;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.my-tickets-section h2 {
    margin-bottom: 2rem;
    color: #2c3e50;
    font-size: 2rem;
    text-align: center;
    position: relative;
    padding-bottom: 15px;
}

.my-tickets-section h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #3498db, #2ecc71);
}

.tickets-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
}

.ticket-card {
    background-color: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #f1f1f1;
}

.ticket-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.ticket-header {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    background-color: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ticket-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.2rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}

.ticket-date {
    color: #7f8c8d;
    font-size: 0.9rem;
    font-weight: 500;
}

.ticket-content {
    padding: 1.5rem;
    color: #34495e;
    max-height: 150px;
    overflow-y: auto;
    line-height: 1.6;
}

.ticket-footer {
    padding: 1.5rem;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f8f9fa;
}

.ticket-status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.ticket-status.nouveau {
    background-color: rgba(241, 196, 15, 0.2);
    color: #f39c12;
    border: 1px solid rgba(241, 196, 15, 0.3);
}

.ticket-status.lu {
    background-color: rgba(52, 152, 219, 0.2);
    color: #2980b9;
    border: 1px solid rgba(52, 152, 219, 0.3);
}

.ticket-status.traité {
    background-color: rgba(46, 204, 113, 0.2);
    color: #27ae60;
    border: 1px solid rgba(46, 204, 113, 0.3);
}

.ticket-actions {
    display: flex;
    gap: 10px;
}

.edit-btn, .delete-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.edit-btn {
    background-color: rgba(52, 152, 219, 0.2);
    color: #2980b9;
    border: 1px solid rgba(52, 152, 219, 0.3);
}

.edit-btn:hover {
    background-color: #3498db;
    color: white;
    border-color: #3498db;
}

.delete-btn {
    background-color: rgba(231, 76, 60, 0.2);
    color: #c0392b;
    border: 1px solid rgba(231, 76, 60, 0.3);
}

.delete-btn:hover {
    background-color: #e74c3c;
    color: white;
    border-color: #e74c3c;
}

.no-tickets {
    text-align: center;
    color: #7f8c8d;
    font-size: 1.1rem;
    padding: 3rem;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    border: 1px dashed #ddd;
}

/* Contact hero section */
.contact-hero {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 3rem auto;
    padding: 2rem;
    position: relative;
    z-index: 1;
    background: rgba(255, 255, 255, 0.85);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.contact-hero h1 {
    color: #2c3e50;
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.contact-hero p {
    color: #7f8c8d;
    font-size: 1.2rem;
    line-height: 1.6;
}

/* Chatbot styling */
.chatbot-toggle {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3498db, #2ecc71);
    color: white;
    border: none;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    z-index: 999;
    transition: all 0.3s ease;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

.chatbot-widget {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    display: none;
    flex-direction: column;
    overflow: hidden;
}

@media (max-width: 992px) {
    .contact-container {
        grid-template-columns: 1fr;
    }
    
    .tickets-container {
        grid-template-columns: 1fr;
    }
    
    .chatbot-widget {
        width: calc(100% - 60px);
        right: 30px;
        left: 30px;
    }
}

@media (max-width: 768px) {
    .ticket-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .ticket-date {
        margin-top: 0.5rem;
    }
    
    .ticket-footer {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .ticket-actions {
        margin-top: 1rem;
        width: 100%;
        justify-content: space-between;
    }
    
    .contact-hero h1 {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .chatbot-widget {
        bottom: 80px;
        height: calc(100vh - 160px);
        width: calc(100% - 40px);
        right: 20px;
        left: 20px;
    }
    
    .chatbot-toggle {
        width: 50px;
        height: 50px;
        bottom: 20px;
        right: 20px;
        font-size: 1.2rem;
    }
}

/* Admin Response Styles */
.modal-responses {
    margin-top: 20px;
    border-top: 1px solid #eee;
    padding-top: 15px;
}

.admin-response {
    background-color: rgba(0, 255, 255, 0.1);
    border-left: 3px solid #00ffff;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 0 8px 8px 0;
}

.admin-response-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 0.9rem;
    color: #b3b3b3;
}

.admin-response-content {
    color: #e0e0e0;
    white-space: pre-wrap;
}

.no-responses {
    color: #b3b3b3;
    font-style: italic;
}
</style>

<!-- Remove Three.js script and initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prefill contact form if coming from a service page
    const urlParams = new URLSearchParams(window.location.search);
    const prefillMessage = urlParams.get('prefill');
    const prefillEmail = urlParams.get('email');
    
    if (prefillMessage) {
        document.getElementById('contact-message').value = decodeURIComponent(prefillMessage.replace(/\+/g, ' '));
    }
    
    if (prefillEmail) {
        document.getElementById('contact-email').value = decodeURIComponent(prefillEmail);
    }
    
    // Initialize chatbot
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotWidget = document.getElementById('chatbot-widget');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotInput = document.getElementById('chatbot-user-input');
    const chatbotMessages = document.getElementById('chatbot-messages');
    
    let chatbotOpen = false;
    
    chatbotToggle.addEventListener('click', function() {
        chatbotOpen = !chatbotOpen;
        if (chatbotOpen) {
            chatbotWidget.style.display = 'flex';
            chatbotToggle.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            chatbotWidget.style.display = 'none';
            chatbotToggle.innerHTML = '<i class="fas fa-robot"></i>';
        }
    });
    
    chatbotClose.addEventListener('click', function() {
        chatbotOpen = false;
        chatbotWidget.style.display = 'none';
        chatbotToggle.innerHTML = '<i class="fas fa-robot"></i>';
    });
    
    function addBotMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chatbot-message bot';
        messageDiv.innerHTML = `<div class="message-content">${text}</div>`;
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
    
    function addUserMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chatbot-message user';
        messageDiv.innerHTML = `<div class="message-content">${text}</div>`;
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
    
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (message === '') return;
        
        addUserMessage(message);
        chatbotInput.value = '';
        
        // Simulate AI response (in a real app, this would call an API)
        setTimeout(() => {
            const response = generateAIResponse(message);
            addBotMessage(response);
        }, 1000);
    }
    
    chatbotSend.addEventListener('click', sendMessage);
    
    chatbotInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Simple AI response generator
    function generateAIResponse(message) {
        const lowerMsg = message.toLowerCase();
        
        if (lowerMsg.includes('hello') || lowerMsg.includes('hi')) {
            return "Hello there! How can I assist you with Clyptor services today?";
        } else if (lowerMsg.includes('carpool') || lowerMsg.includes('covoiturage')) {
            return "Our carpooling service connects drivers with passengers heading the same way. You can save money on your commute or long trips by sharing rides. Would you like help finding a ride or listing your vehicle?";
        } else if (lowerMsg.includes('home') || lowerMsg.includes('rent') || lowerMsg.includes('property')) {
            return "Our home rental platform allows you to list or find vacation rentals, apartments, and rooms. Are you looking to rent out your property or find a place to stay?";
        } else if (lowerMsg.includes('car') || lowerMsg.includes('vehicle')) {
            return "The car rental service lets you rent vehicles from local owners by the hour, day, or week. You can also list your own car to earn money when you're not using it. How can I help with car rentals?";
        } else if (lowerMsg.includes('account') || lowerMsg.includes('login') || lowerMsg.includes('register')) {
            return "You can create an account or login from any page using the top navigation. Having an account allows you to create listings, contact other users, and manage your posts. Would you like me to direct you to the registration page?";
        } else if (lowerMsg.includes('help') || lowerMsg.includes('support')) {
            return "I can help answer questions about our services. For specific account issues or complaints, please use the contact form on this page to reach our human support team.";
        } else if (lowerMsg.includes('thank') || lowerMsg.includes('thanks')) {
            return "You're welcome! Is there anything else I can help you with?";
        } else if (lowerMsg.includes('bye') || lowerMsg.includes('goodbye')) {
            return "Goodbye! Don't hesitate to reach out if you have more questions.";
        } else {
            const randomResponses = [
                "I'm here to help with Clyptor services. Could you tell me more about what you need?",
                "That's an interesting question. I can provide information about our carpooling, home rental, and car rental services. Which one are you interested in?",
                "I'm still learning! For complex questions, please use the contact form to reach our support team.",
                "Let me help you with that. Are you asking about our sharing services?"
            ];
            return randomResponses[Math.floor(Math.random() * randomResponses.length)];
        }
    }
});
</script>

<!-- Replace the footer include with direct HTML -->
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
                <li><a href="covoiturage.html">Carpooling</a></li>
                <li><a href="home-rent.html">Home Rent</a></li>
                <li><a href="car-rent.html">Car Rent</a></li>
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

<!-- Add mobile menu toggle script -->
<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const mainNav = document.querySelector('.main-nav');
        
        if (mobileMenuToggle && mainNav) {
            mobileMenuToggle.addEventListener('click', function() {
                mainNav.classList.toggle('active');
            });
        }
    });
</script>

</body>
</html> 