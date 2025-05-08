<?php
// Inclure le fichier de configuration si ce n'est pas déjà fait
if(!defined('BASE_URL')) {
    require_once '../../../config.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Clyptor' : 'Clyptor'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/style.css">
    <!-- Notification CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/View/frontoffice/css/notifications.css">
</head>
<body>
    <!-- Toast notification container -->
    <div id="toast-container"></div>
    
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo BASE_URL; ?>">Clyptor</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?page=car">Location de voitures</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?page=home">Location de maisons</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?page=covoiturage">Covoiturage</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>?page=contact">Contact</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <!-- Notification Icon -->
                            <li class="nav-item dropdown me-2">
                                <a class="nav-link notification-badge" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-count="0">
                                    <i class="fas fa-bell"></i>
                                </a>
                                <!-- Notifications Dropdown Menu -->
                                <div id="notifications-dropdown" class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                                    <div class="notification-header">
                                        <h3>Notifications</h3>
                                        <button class="mark-all-read">Mark all as read</button>
                                    </div>
                                    <ul class="notification-list" id="notification-list">
                                        <li class="empty-notifications">No new notifications</li>
                                    </ul>
                                </div>
                            </li>
                            <!-- User Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>?page=user&action=dashboard">Tableau de bord</a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>?page=user&action=profile">Mon profil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>?page=user&action=logout">Déconnexion</a></li>
                                </ul>
                            </li>
                            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo BASE_URL; ?>?page=admin">Admin</a>
                                </li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>?page=user&action=login">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>?page=user&action=register">Inscription</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4">
        <?php
        // Afficher les messages de succès
        if(isset($_SESSION['message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo $_SESSION['message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['message']);
        }
        ?> 

<?php if(isset($_SESSION['user_id'])): ?>
<!-- Notification System JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const notificationBadge = document.querySelector('.notification-badge');
    const notificationsDropdown = document.getElementById('notifications-dropdown');
    const notificationList = document.getElementById('notification-list');
    const markAllReadBtn = document.querySelector('.mark-all-read');
    const toastContainer = document.getElementById('toast-container');
    
    // Set up localStorage for tracking notifications
    let lastCheckTime = localStorage.getItem('lastNotificationCheck');
    if (!lastCheckTime) {
        lastCheckTime = new Date().toISOString();
        localStorage.setItem('lastNotificationCheck', lastCheckTime);
    }
    
    // Variables for notification state
    let notifications = [];
    let unreadCount = 0;
    
    // Toggle notifications dropdown
    notificationBadge.addEventListener('click', function(e) {
        e.preventDefault();
        notificationsDropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationBadge.contains(e.target) && !notificationsDropdown.contains(e.target)) {
            notificationsDropdown.classList.remove('show');
        }
    });
    
    // Mark all notifications as read
    markAllReadBtn.addEventListener('click', function() {
        if (notifications.length > 0) {
            notifications.forEach(notification => {
                notification.read = true;
            });
            unreadCount = 0;
            updateNotificationBadge();
            renderNotifications();
            localStorage.setItem('notifications', JSON.stringify(notifications));
        }
    });
    
    // Function to check for new responses
    function checkNewResponses() {
        // Prepare the URL with the last check timestamp
        const url = `<?php echo BASE_URL; ?>/View/frontoffice/check_new_responses.php?last_check=${encodeURIComponent(lastCheckTime)}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.new_responses > 0) {
                    // Process new responses
                    processNewResponses(data.responses);
                    
                    // Update last check time
                    lastCheckTime = new Date().toISOString();
                    localStorage.setItem('lastNotificationCheck', lastCheckTime);
                }
            })
            .catch(error => {
                console.error('Error checking for new responses:', error);
            });
    }
    
    // Process new responses and create notifications
    function processNewResponses(responses) {
        const storedNotifications = JSON.parse(localStorage.getItem('notifications')) || [];
        const notificationIds = storedNotifications.map(n => n.responseId);
        
        let hasNewNotifications = false;
        
        responses.forEach(response => {
            // Check if notification already exists
            if (!notificationIds.includes(response.id)) {
                const newNotification = {
                    responseId: response.id,
                    messageId: response.message_id,
                    title: 'New Response from Admin',
                    message: 'A new response has been added to your ticket',
                    time: response.created,
                    read: false
                };
                
                storedNotifications.push(newNotification);
                hasNewNotifications = true;
            }
        });
        
        if (hasNewNotifications) {
            // Save updated notifications
            localStorage.setItem('notifications', JSON.stringify(storedNotifications));
            
            // Update UI
            notifications = storedNotifications;
            updateUnreadCount();
            updateNotificationBadge();
            renderNotifications();
            
            // Show toast notification
            showToastNotification('New Response', 'You have a new response from the admin!');
        }
    }
    
    // Update unread count
    function updateUnreadCount() {
        unreadCount = notifications.filter(n => !n.read).length;
    }
    
    // Update notification badge
    function updateNotificationBadge() {
        notificationBadge.setAttribute('data-count', unreadCount);
    }
    
    // Render notifications in the dropdown
    function renderNotifications() {
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
                localStorage.setItem('notifications', JSON.stringify(notifications));
                updateUnreadCount();
                updateNotificationBadge();
                renderNotifications();
                
                // Redirect to contact page with ticket highlighted
                window.location.href = '<?php echo BASE_URL; ?>?page=contact&highlight=' + notification.messageId;
            });
            
            notificationList.appendChild(notificationItem);
        });
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
    
    // Close toast
    function closeToast(toast) {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
    
    // Load notifications from localStorage
    function loadNotifications() {
        notifications = JSON.parse(localStorage.getItem('notifications')) || [];
        updateUnreadCount();
        updateNotificationBadge();
        renderNotifications();
    }
    
    // Initialize
    loadNotifications();
    
    // Check for new responses initially
    checkNewResponses();
    
    // Set up periodic checking (every 30 seconds)
    setInterval(checkNewResponses, 30000);
});
</script>
<?php endif; ?> 