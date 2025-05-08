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
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Debug script for the notification system -->
    <script>
    // Store user debug flag
    const urlParams = new URLSearchParams(window.location.search);
    const debugMode = false;
    
    // Log debug message if in debug mode
    function debugLog(message, data) {
        // Empty function - debug removed
    }
    
    // Initialize with test notifications if debug=true
    document.addEventListener('DOMContentLoaded', function() {
        // Debug functionality removed
    });
    </script>
</head>
<body>
    <!-- Toast notification container -->
    <div id="toast-container"></div>
    
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
                <!-- Notification Icon -->
                <div class="notification-wrapper">
                    <a href="#" class="notification-badge" id="notificationsDropdown" data-count="0">
                        <i class="fas fa-bell"></i>
                    </a>
                    <!-- Notifications Dropdown Menu -->
                    <div id="notifications-dropdown">
                        <div class="notification-header">
                            <h3>Notifications</h3>
                            <button class="mark-all-read">Mark all as read</button>
                        </div>
                        <ul class="notification-list" id="notification-list">
                            <li class="empty-notifications">No new notifications</li>
                        </ul>
                    </div>
                </div>
                
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
    <div id="contact-form-errors" style="color: red;"></div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    const nameInput = document.getElementById('contact-name');
    const emailInput = document.getElementById('contact-email');
    const subjectInput = document.getElementById('contact-subject');
    const messageInput = document.getElementById('contact-message');
    const errorsDiv = document.getElementById('contact-form-errors');

    function validateEmail(email) {
        // Simple email regex
        return /^\S+@\S+\.\S+$/.test(email);
    }

    contactForm.addEventListener('submit', function(e) {
        let errors = [];
        errorsDiv.innerHTML = '';

        if (nameInput && nameInput.value.trim().length < 2) {
            errors.push('Le nom doit contenir au moins 2 caractères.');
        }
        if (emailInput && !validateEmail(emailInput.value.trim())) {
            errors.push('Veuillez entrer un email valide.');
        }
        if (subjectInput && subjectInput.value.trim().length < 3) {
            errors.push('Le sujet doit contenir au moins 3 caractères.');
        }
        if (messageInput && messageInput.value.trim().length < 10) {
            errors.push('Le message doit contenir au moins 10 caractères.');
        }

        if (errors.length > 0) {
            e.preventDefault();
            errorsDiv.innerHTML = errors.map(err => `<div>${err}</div>`).join('');
        }
    });

    // Effacer les erreurs en temps réel
    [nameInput, emailInput, subjectInput, messageInput].forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                errorsDiv.innerHTML = '';
            });
        }
    });
});
</script>
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
    
    // Check for ticket to highlight (from notification)
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const highlightTicketId = urlParams.get('highlight');
        
        if (highlightTicketId) {
            // Find the ticket and highlight it
            const tickets = document.querySelectorAll('.ticket-card');
            let ticketFound = false;
            
            tickets.forEach(ticket => {
                const links = ticket.querySelectorAll('a');
                
                // Find the ticket ID from the edit link
                links.forEach(link => {
                    if (link.href.includes('action=edit&id=')) {
                        const match = link.href.match(/id=(\d+)/);
                        if (match && match[1] === highlightTicketId) {
                            // Highlight the ticket
                            ticket.classList.add('highlight-ticket');
                            ticket.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            
                            // Open the ticket details after a short delay
                            setTimeout(() => {
                                viewTicketDetails(parseInt(highlightTicketId));
                            }, 800);
                            
                            ticketFound = true;
                        }
                    }
                });
            });
            
            // Remove the highlight parameter from the URL to avoid rehighlighting on refresh
            if (ticketFound) {
                const newUrl = window.location.pathname + window.location.search.replace(/[?&]highlight=\d+/, '');
                window.history.replaceState({}, document.title, newUrl);
            }
        }
    });
    </script>
    <?php endif; ?>

    <!-- AI Chatbot Widget - Include from chatbot.php -->
    <?php include_once "chatbot.php"; ?>

</main>

<!-- Add CSS for highlighted ticket -->
<style>
.highlight-ticket {
    animation: highlight-pulse 2s ease-in-out;
    box-shadow: 0 0 15px rgba(0, 255, 0, 0.7) !important;
    position: relative;
    z-index: 1;
}

@keyframes highlight-pulse {
    0% { box-shadow: 0 0 15px rgba(0, 255, 0, 0.7); }
    50% { box-shadow: 0 0 25px rgba(0, 255, 0, 0.9); }
    100% { box-shadow: 0 0 15px rgba(0, 255, 0, 0.7); }
}

/* Notification styles specific to the contact page */
.notification-wrapper {
    display: inline-flex;
    margin-right: 20px;
    position: relative;
}

.auth-buttons .notification-badge {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transition: all 0.3s ease;
}

.auth-buttons .notification-badge:hover {
    background: rgba(0, 255, 0, 0.2);
    transform: scale(1.1);
}

.auth-buttons .notification-badge[data-count]:after {
    position: absolute;
    right: -6px;
    top: -6px;
    content: attr(data-count);
    font-size: 11px;
    padding: 3px;
    min-width: 20px;
    height: 20px;
    line-height: 14px;
    border-radius: 50%;
    background-color: #ff4b5c;
    color: white;
    text-align: center;
    font-weight: bold;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
}

/* Toast notification style fixes */
.toast-notification {
    top: 80px;
    right: 20px;
    z-index: 9999;
}

/* Notification dropdown */
.notification-wrapper {
    position: relative;
    display: inline-block;
}

.notification-badge {
    position: relative;
    display: inline-block;
    font-size: 20px;
    color: #fff;
    cursor: pointer;
    margin-right: 15px;
}

.notification-badge:after {
    content: attr(data-count);
    position: absolute;
    top: -8px;
    right: -8px;
    font-size: 11px;
    background-color: var(--danger);
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    line-height: 1;
    font-weight: bold;
    display: attr(data-count) ? 'block' : 'none';
}

/* Hide badge if count is 0 */
.notification-badge[data-count="0"]:after {
    display: none;
}

/* New response highlight styling */
.ticket-card.new-response {
    animation: pulse-highlight 2s infinite;
    box-shadow: 0 0 8px rgba(var(--primary-rgb), 0.6);
    border-left: 3px solid var(--primary);
}

@keyframes pulse-highlight {
    0% {
        box-shadow: 0 0 8px rgba(var(--primary-rgb), 0.4);
    }
    50% {
        box-shadow: 0 0 12px rgba(var(--primary-rgb), 0.7);
    }
    100% {
        box-shadow: 0 0 8px rgba(var(--primary-rgb), 0.4);
    }
}

/* Highlight for clicked notification */
.ticket-card.highlight-ticket {
    animation: highlight-flash 2s;
    border-left: 3px solid var(--secondary);
}

@keyframes highlight-flash {
    0%, 100% {
        background-color: rgba(var(--secondary-rgb), 0.1);
    }
    50% {
        background-color: rgba(var(--secondary-rgb), 0.3);
    }
}

/* Notification dropdown */
.notifications-dropdown {
    position: absolute;
    right: 0;
    top: 36px;
    width: 300px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.notifications-dropdown.show {
    display: block;
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
}

.notifications-title {
    font-weight: bold;
    font-size: 14px;
}

.mark-all-read {
    font-size: 12px;
    color: var(--primary);
    cursor: pointer;
}

.notification-list {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

.notification-item {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f9f9f9;
}

.notification-item.unread {
    background-color: rgba(var(--primary-rgb), 0.05);
    border-left: 3px solid var(--primary);
}

.notification-title {
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 3px;
}

.notification-message {
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}

.notification-time {
    font-size: 11px;
    color: #999;
}

.empty-notifications {
    padding: 15px;
    text-align: center;
    color: #777;
    font-size: 13px;
}

/* Toast notifications */
#toast-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    background-color: white;
    color: #333;
    padding: 15px 20px;
    border-radius: 6px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease;
    margin-top: 10px;
    width: 300px;
    border-left: 4px solid var(--primary);
}

.toast.show {
    opacity: 1;
    transform: translateY(0);
}

.toast-header {
    font-weight: bold;
    margin-bottom: 5px;
    display: flex;
    justify-content: space-between;
}

.toast-close {
    cursor: pointer;
    opacity: 0.7;
}

.toast-body {
    font-size: 13px;
}
</style>

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

<!-- Add notification system JavaScript -->
<?php if(isset($_SESSION['user_id'])): ?>
<script>
// Main notification system
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const notificationBadge = document.querySelector('.notification-badge');
    const notificationsDropdown = document.getElementById('notifications-dropdown');
    const notificationList = document.getElementById('notification-list');
    const markAllReadBtn = document.querySelector('.mark-all-read');
    const toastContainer = document.getElementById('toast-container');
    
    // Variables for notification state
    let notifications = [];
    let unreadCount = 0;
    
    // Toggle notifications dropdown
    if (notificationBadge) {
        notificationBadge.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            notificationsDropdown.classList.toggle('show');
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (notificationsDropdown && !notificationBadge.contains(e.target) && !notificationsDropdown.contains(e.target)) {
            notificationsDropdown.classList.remove('show');
        }
    });
    
    // Mark all notifications as read
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (notifications.length > 0) {
                notifications.forEach(notification => {
                    notification.read = true;
                });
                unreadCount = 0;
                updateNotificationBadge();
                renderNotifications();
                localStorage.setItem('clyptor_notifications', JSON.stringify(notifications));
            }
        });
    }

    // ... Rest of the notification code ...
    
    // Show a test toast notification in debug mode
    if (debugMode) {
        setTimeout(() => {
            showToastNotification('Debug Mode Active', 'The notification system is in debug mode');
        }, 1000);
    }
    
    // Initialize notifications
    loadNotifications();
    
    // Show notifications dropdown if in debug mode
    if (debugMode && notificationsDropdown) {
        setTimeout(() => {
            notificationsDropdown.classList.add('show');
        }, 500);
    }
    
    // Fetch all existing responses
    fetchAllResponses();
    
    // Check for new responses after a delay
    setTimeout(checkNewResponses, 2000);
    
    // Set up periodic checking (every 15 seconds)
    setInterval(checkNewResponses, 15000);
    
    // Function to fetch all existing admin responses
    function fetchAllResponses() {
        fetch('fetch_all_responses.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.responses && data.responses.length > 0) {
                    // Process all existing responses
                    processExistingResponses(data.responses);
                }
            })
            .catch(error => {
                console.error('Error fetching existing responses:', error);
            });
    }
    
    // Function to process existing responses
    function processExistingResponses(responses) {
        const storedNotifications = JSON.parse(localStorage.getItem('clyptor_notifications')) || [];
        const notificationIds = storedNotifications.map(n => n.responseId);
        
        let hasNewNotifications = false;
        
        responses.forEach(response => {
            // Check if notification already exists
            if (!notificationIds.includes(parseInt(response.id))) {
                const newNotification = {
                    responseId: parseInt(response.id),
                    messageId: parseInt(response.message_id),
                    title: 'Response to: ' + (response.message_subject || 'Your ticket'),
                    message: truncateText(response.response_text, 50),
                    time: response.created,
                    read: false
                };
                
                storedNotifications.push(newNotification);
                hasNewNotifications = true;
            }
        });
        
        if (hasNewNotifications) {
            // Save updated notifications
            localStorage.setItem('clyptor_notifications', JSON.stringify(storedNotifications));
            
            // Update UI
            notifications = storedNotifications;
            updateUnreadCount();
            updateNotificationBadge();
            renderNotifications();
            
            // Show toast for debug mode
            if (debugMode) {
                showToastNotification('New Notifications Found', `Found ${responses.length} admin responses`);
            }
        } else {
            // Still load the notifications
            notifications = storedNotifications;
            updateUnreadCount();
            updateNotificationBadge();
            renderNotifications();
        }
    }
    
    // Function to check for new responses
    function checkNewResponses() {
        // Get the most recent notification time
        const mostRecentTime = getMostRecentNotificationTime();
        
        // Prepare the URL with the most recent notification time
        const url = `check_new_responses.php?last_check=${encodeURIComponent(mostRecentTime)}`;
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response was not ok: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.new_responses > 0) {
                    // Process new responses
                    processNewResponses(data.responses);
                    
                    // If we have the ticket list and there are new messages for those tickets,
                    // update their display as well
                    updateTicketsIfNeeded(data.responses);
                }
            })
            .catch(error => {
                console.error('Error checking for new responses:', error);
                // Don't let one error stop future checks - we'll try again next interval
            });
    }
    
    // Function to update tickets in the ticket list if they received new responses
    function updateTicketsIfNeeded(responses) {
        if (!responses || responses.length === 0) return;
        
        // Get all ticket cards
        const ticketCards = document.querySelectorAll('.ticket-card');
        if (!ticketCards || ticketCards.length === 0) return;
        
        // Extract message IDs from responses
        const updatedMessageIds = responses.map(r => parseInt(r.message_id));
        
        // For each ticket card, check if it needs updating
        ticketCards.forEach(card => {
            const links = card.querySelectorAll('a');
            
            // Find the ticket ID from the edit link
            links.forEach(link => {
                if (link.href.includes('action=edit&id=')) {
                    const match = link.href.match(/id=(\d+)/);
                    if (match && match[1] && updatedMessageIds.includes(parseInt(match[1]))) {
                        // This ticket has a new response - update its display
                        // Add a visual indicator
                        card.classList.add('new-response');
                        
                        // If the status is still "Nouveau", update it to "Lu"
                        const statusElement = card.querySelector('.ticket-status');
                        if (statusElement && statusElement.textContent.trim() === 'Nouveau') {
                            statusElement.textContent = 'Lu';
                            statusElement.className = 'ticket-status lu';
                            card.setAttribute('data-status', 'lu');
                        }
                    }
                }
            });
        });
    }
    
    // Get the most recent notification time
    function getMostRecentNotificationTime() {
        if (notifications.length === 0) {
            // If no notifications, use a date from the past
            return '2000-01-01T00:00:00Z';
        }
        
        // Sort notifications by time (newest first)
        const sortedNotifications = [...notifications].sort((a, b) => new Date(b.time) - new Date(a.time));
        
        // Return the most recent time
        return sortedNotifications[0].time;
    }
    
    // Function to truncate text
    function truncateText(text, maxLength) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }
    
    // Process new responses and create notifications
    function processNewResponses(responses) {
        const storedNotifications = JSON.parse(localStorage.getItem('clyptor_notifications')) || [];
        const notificationIds = storedNotifications.map(n => n.responseId);
        
        let hasNewNotifications = false;
        let newResponsesCount = 0;
        
        responses.forEach(response => {
            // Check if notification already exists
            if (!notificationIds.includes(parseInt(response.id))) {
                const newNotification = {
                    responseId: parseInt(response.id),
                    messageId: parseInt(response.message_id),
                    title: 'New Response from Admin',
                    message: truncateText(response.response_text, 50),
                    time: response.created,
                    read: false
                };
                
                storedNotifications.push(newNotification);
                hasNewNotifications = true;
                newResponsesCount++;
            }
        });
        
        if (hasNewNotifications) {
            // Save updated notifications
            localStorage.setItem('clyptor_notifications', JSON.stringify(storedNotifications));
            
            // Update UI
            notifications = storedNotifications;
            updateUnreadCount();
            updateNotificationBadge();
            renderNotifications();
            
            // Show toast notification
            showToastNotification(
                newResponsesCount > 1 ? 'New Responses' : 'New Response', 
                newResponsesCount > 1 ? `You have ${newResponsesCount} new responses from admin!` : 'You have a new response from the admin!'
            );
            
            // If we're on a ticket detail view and this is related to the current ticket
            updateOpenModalIfNeeded(responses);
        }
    }
    
    // Update the modal if it's open and showing a ticket related to the new responses
    function updateOpenModalIfNeeded(responses) {
        const modal = document.getElementById('ticket-detail-modal');
        if (!modal || modal.style.display !== 'block') return;
        
        // Get currently displayed ticket ID
        const ticketIdElement = document.getElementById('modal-ticket-id');
        if (!ticketIdElement) return;
        
        const currentTicketId = ticketIdElement.textContent;
        
        // Check if any of the new responses are for this ticket
        const matchingResponses = responses.filter(r => r.message_id.toString() === currentTicketId);
        
        if (matchingResponses.length > 0) {
            // Reload responses for this ticket
            loadAdminResponses(currentTicketId);
        }
    }
    
    // Update unread count
    function updateUnreadCount() {
        unreadCount = notifications.filter(n => !n.read).length;
    }
    
    // Update notification badge
    function updateNotificationBadge() {
        if (notificationBadge) {
            notificationBadge.setAttribute('data-count', unreadCount.toString());
        }
    }
    
    // Render notifications in the dropdown
    function renderNotifications() {
        if (!notificationList) return;
        
        if (notifications.length === 0) {
            notificationList.innerHTML = '<li class="empty-notifications">No new notifications</li>';
            return;
        }
        
        // Sort notifications by time (newest first)
        notifications.sort((a, b) => new Date(b.time) - new Date(a.time));
        
        notificationList.innerHTML = '';
        
        notifications.forEach(notification => {
            const notificationItem = document.createElement('li');
            notificationItem.className = 'notification-item' + (notification.read ? '' : ' unread');
            notificationItem.dataset.messageId = notification.messageId;
            
            notificationItem.innerHTML = `
                <div class="notification-title">${notification.title}</div>
                <div class="notification-message">${notification.message}</div>
                <div class="notification-time">${formatDate(notification.time)}</div>
            `;
            
            // Handle click on notification
            notificationItem.addEventListener('click', function() {
                // Mark notification as read
                notification.read = true;
                localStorage.setItem('clyptor_notifications', JSON.stringify(notifications));
                updateUnreadCount();
                updateNotificationBadge();
                renderNotifications();
                
                // Highlight and scroll to the ticket
                highlightTicket(notification.messageId);
            });
            
            notificationList.appendChild(notificationItem);
        });
    }
    
    // Highlight and scroll to a ticket
    function highlightTicket(messageId) {
        const tickets = document.querySelectorAll('.ticket-card');
        let ticketFound = false;
        
        tickets.forEach(ticket => {
            const links = ticket.querySelectorAll('a');
            
            // Find the ticket ID from the edit link
            links.forEach(link => {
                if (link.href.includes('action=edit&id=')) {
                    const match = link.href.match(/id=(\d+)/);
                    if (match && match[1] === messageId.toString()) {
                        // Highlight the ticket
                        ticket.classList.add('highlight-ticket');
                        ticket.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // Open the ticket details after a short delay
                        setTimeout(() => {
                            if (typeof viewTicketDetails === 'function') {
                                viewTicketDetails(parseInt(messageId));
                            }
                        }, 800);
                        
                        ticketFound = true;
                    }
                }
            });
        });
        
        // If not found, check if we need to add highlight parameter to URL
        if (!ticketFound) {
            // Redirect to contact page with ticket highlighted
            window.location.href = 'contact.php?highlight=' + messageId;
        }
    }
    
    // Format date for notifications
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);
        
        if (diffMins < 1) {
            return 'Just now';
        } else if (diffMins < 60) {
            return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
        } else if (diffHours < 24) {
            return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        } else if (diffDays < 7) {
            return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        } else {
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
    }
    
    // Show toast notification
    function showToastNotification(title, message) {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas fa-bell"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close">&times;</button>
        `;
        
        if (toastContainer) {
            toastContainer.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            // Auto close after 5 seconds
            setTimeout(() => {
                closeToast(toast);
            }, 5000);
            
            // Close button handler
            toast.querySelector('.toast-close').addEventListener('click', function() {
                closeToast(toast);
            });
        }
    }
    
    // Close toast
    function closeToast(toast) {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
    
    // Load notifications from localStorage
    function loadNotifications() {
        try {
            notifications = JSON.parse(localStorage.getItem('clyptor_notifications')) || [];
            updateUnreadCount();
            updateNotificationBadge();
            renderNotifications();
        } catch (error) {
            console.error('Error loading notifications:', error);
            notifications = [];
            localStorage.setItem('clyptor_notifications', JSON.stringify([]));
        }
    }
});
</script>
<?php endif; ?>

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