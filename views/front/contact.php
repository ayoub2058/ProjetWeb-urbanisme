<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clyptor - Contact Us</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/hf.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

<?php
require_once '../../controllers/ContactController.php';

$controller = new ContactController();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_id'])) {
    $data = [
        'name' => $_POST['contact-name'],
        'email' => $_POST['contact-email'],
        'subject' => $_POST['contact-subject'],
        'message' => $_POST['contact-message'],
    ];
    $controller->createMessage($data);
    echo "<script>alert('Your message has been sent successfully!');</script>";
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $controller->deleteMessage($_GET['delete_id']);
    echo "<script>alert('Message deleted successfully!'); window.location.href = 'contact.php';</script>";
}

// Handle edit action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $data = [
        'name' => $_POST['edit-name'],
        'email' => $_POST['edit-email'],
        'subject' => $_POST['edit-subject'],
        'message' => $_POST['edit-message'],
    ];
    $controller->updateMessage($_POST['edit_id'], $data);
    echo "<script>alert('Message updated successfully!'); window.location.href = 'contact.php';</script>";
}

// Fetch all messages
$messages = $controller->getAllMessages();
?>

    <class="contact-main">
        <section class="contact-hero">
            <h1>Contact Us</h1>
            <p>Have questions or feedback? We'd love to hear from you!</p>
        </section>

        <section class="contact-container">
            <div class="contact-form-container">
                <h2>Send Us a Message</h2>
                <form id="contact-form" class="contact-form" method="POST">
                    <div class="form-group">
                        <label for="contact-name">Name</label>
                        <input type="text" id="contact-name" name="contact-name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Email</label>
                        <input type="email" id="contact-email" name="contact-email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-subject">Subject</label>
                        <input type="text" id="contact-subject" name="contact-subject" placeholder="Subject" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-message">Message</label>
                        <textarea id="contact-message" name="contact-message" placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>

            <div class="contact-info-container">
                <h2>Submitted Messages</h2>
                <div class="contact-info">
                    <?php foreach ($messages as $msg): ?>
                        <div class="info-item">
                            <p><strong>Name:</strong> <?= htmlspecialchars($msg['name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($msg['email']) ?></p>
                            <p><strong>Subject:</strong> <?= htmlspecialchars($msg['subject']) ?></p>
                            <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                            <p><small><strong>Submitted At:</strong> <?= $msg['created_at'] ?></small></p>
                            <a href="?delete_id=<?= $msg['message_id'] ?>" onclick="return confirm('Are you sure you want to delete this message?');" class="btn btn-danger">Delete</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="edit_id" value="<?= $msg['message_id'] ?>">
                                <input type="text" name="edit-name" value="<?= htmlspecialchars($msg['name']) ?>" required>
                                <input type="email" name="edit-email" value="<?= htmlspecialchars($msg['email']) ?>" required>
                                <input type="text" name="edit-subject" value="<?= htmlspecialchars($msg['subject']) ?>" required>
                                <textarea name="edit-message" required><?= htmlspecialchars($msg['message']) ?></textarea>
                                <button type="submit" class="btn btn-primary">Edit</button>
                            </form>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

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
                        <li><a href="carpooling.php">Carpooling</a></li>
                        <li><a href="home-rent.php">Home Rent</a></li>
                        <li><a href="car-rent.php">Car Rent</a></li>
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
             </class>
             </body>   
    <script src="js/main.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/contact.js"></script>
    <script>
        function openEditModal(message) {
            // Populate the modal form with the message data
            document.getElementById('edit-id').value = message.message_id;
            document.getElementById('edit-name').value = message.name;
            document.getElementById('edit-email').value = message.email;
            document.getElementById('edit-subject').value = message.subject;
            document.getElementById('edit-message').value = message.message;

            // Display the modal
            document.getElementById('edit-modal').style.display = 'block';
        }

        function closeEditModal() {
            // Hide the modal
            document.getElementById('edit-modal').style.display = 'none';
        }

        // Prefill contact form if coming from a service page
        document.addEventListener('DOMContentLoaded', function() {
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
                    chatbotWidget.style.display = 'block';
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