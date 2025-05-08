<?php
// Include necessary files
require_once '../../config.php';
require_once MODEL_PATH . '/Database.php';
require_once MODEL_PATH . '/Message.php';
require_once MODEL_PATH . '/Response.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    echo "You must be logged in to test this functionality.";
    exit;
}

// Initialize the database connection
$database = new Database();
$db = $database->getConnection();

// Get the Message and Response objects
$message = new Message($db);
$response = new Response($db);

// Get the messages for the current user
$user_email = $_SESSION['email'];
$user_messages = $message->getUserMessages($user_email);

// Check if the user has any messages
if (empty($user_messages)) {
    echo "You don't have any messages to test notifications with. Please create a ticket first.";
    exit;
}

// Get the first message to use for testing
$test_message = $user_messages[0];
$message_id = $test_message['id'];

// Handle form submission to create a test response
$response_added = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_response'])) {
    // Create a new test response
    $response->message_id = $message_id;
    $response->admin_name = "Test Admin";
    $response->response_text = "This is a test response from the admin at " . date('Y-m-d H:i:s');
    
    if ($response->create()) {
        // Update the message status to Traité (Processed)
        $message->id = $message_id;
        $message->status = "Traité";
        $message->updateStatus();
        
        $response_added = true;
    }
}

// Get all responses for this message
$message_responses = $response->getResponsesForMessage($message_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Notification System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .card {
            margin-bottom: 20px;
        }
        .response {
            border-left: 4px solid #28a745;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f8f8f8;
        }
        .response-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Test Notification System</h1>
        
        <?php if ($response_added): ?>
        <div class="alert alert-success">
            Test response has been added successfully! Refresh the contact page to see the notification.
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Message Details
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($test_message['subject']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($test_message['message']); ?></p>
                        <div class="d-flex justify-content-between">
                            <span>Status: <strong><?php echo htmlspecialchars($test_message['status']); ?></strong></span>
                            <span>Created: <?php echo date('d/m/Y H:i', strtotime($test_message['created'])); ?></span>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="">
                    <button type="submit" name="add_response" class="btn btn-primary">
                        Add Test Response (Simulate Admin Reply)
                    </button>
                </form>
                
                <div class="mt-4">
                    <a href="contact.php" class="btn btn-secondary">Return to Contact Page</a>
                    <button id="clear-notifications" class="btn btn-danger ms-2">Clear All Notifications</button>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        Debug Tools
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <button id="refresh-notifications" class="btn btn-primary">Fetch Notifications</button>
                            <button id="show-local-storage" class="btn btn-info">Show LocalStorage</button>
                        </div>
                        <div id="debug-output" class="border p-3 bg-light" style="max-height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                            Click buttons above to see debug information.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Existing Responses
                    </div>
                    <div class="card-body">
                        <?php if (empty($message_responses)): ?>
                            <p class="text-muted">No responses yet.</p>
                        <?php else: ?>
                            <?php foreach ($message_responses as $response): ?>
                                <div class="response">
                                    <div class="response-header">
                                        <span><?php echo htmlspecialchars($response['admin_name']); ?></span>
                                        <span><?php echo date('d/m/Y H:i', strtotime($response['created'])); ?></span>
                                    </div>
                                    <div class="response-content">
                                        <?php echo htmlspecialchars($response['response_text']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        Instructions
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Click the "Add Test Response" button to simulate an admin reply to your message</li>
                            <li>Go back to the Contact page</li>
                            <li>You should see a notification badge on the bell icon</li>
                            <li>A toast notification should also appear</li>
                            <li>Click on the notification to go to your ticket</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Clear notifications button
        document.getElementById('clear-notifications').addEventListener('click', function() {
            if (confirm('Are you sure you want to clear all notifications? This will reset your notification history.')) {
                localStorage.setItem('clyptor_notifications', JSON.stringify([]));
                
                // No need to set a last check time anymore, as we now use notification timestamps
                alert('All notifications have been cleared.');
                
                // Refresh debug output
                showLocalStorage();
            }
        });
        
        // Debug tools
        document.getElementById('refresh-notifications').addEventListener('click', function() {
            fetch('fetch_all_responses.php')
                .then(response => response.json())
                .then(data => {
                    const debugOutput = document.getElementById('debug-output');
                    debugOutput.innerHTML = '<strong>Server Response:</strong><br>' + 
                                          JSON.stringify(data, null, 2).replace(/\n/g, '<br>').replace(/ /g, '&nbsp;');
                })
                .catch(error => {
                    document.getElementById('debug-output').textContent = 'Error: ' + error.message;
                });
        });
        
        document.getElementById('show-local-storage').addEventListener('click', showLocalStorage);
        
        function showLocalStorage() {
            const debugOutput = document.getElementById('debug-output');
            const notifications = JSON.parse(localStorage.getItem('clyptor_notifications') || '[]');
            
            const output = {
                'notification_count': notifications.length,
                'unread_count': notifications.filter(n => !n.read).length,
                'notifications': notifications
            };
            
            debugOutput.innerHTML = '<strong>LocalStorage:</strong><br>' + 
                                   JSON.stringify(output, null, 2).replace(/\n/g, '<br>').replace(/ /g, '&nbsp;');
        }
        
        // Show localStorage content on page load
        showLocalStorage();
    </script>
</body>
</html> 