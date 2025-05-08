<?php
// Include necessary files
require_once '../../config.php';
require_once MODEL_PATH . '/Database.php';
require_once MODEL_PATH . '/Response.php';
require_once MODEL_PATH . '/Message.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (session_status() == PHP_SESSION_NONE) {
    @session_start(); // Suppress any notices
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    echo json_encode([
        'error' => 'User not logged in',
        'responses' => []
    ]);
    exit;
}

$user_email = $_SESSION['email'];

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

try {
    // Get all messages belonging to the user
    $message = new Message($db);
    $user_messages = $message->getUserMessages($user_email);
    
    if (empty($user_messages)) {
        echo json_encode([
            'success' => true,
            'responses' => [],
            'debug' => 'No messages found for this user'
        ]);
        exit;
    }
    
    // Extract message IDs
    $message_ids = array_column($user_messages, 'id');
    
    // If there are no message IDs, return empty array
    if (empty($message_ids)) {
        echo json_encode([
            'success' => true,
            'responses' => [],
            'debug' => 'No message IDs extracted'
        ]);
        exit;
    }
    
    // Simplified approach: let's get all responses by iterating through messages
    $all_responses = [];
    $response = new Response($db);
    
    foreach ($message_ids as $message_id) {
        $message_responses = $response->getResponsesForMessage($message_id);
        
        if (!empty($message_responses)) {
            // Add the message subject to each response
            foreach ($message_responses as &$resp) {
                // Find the message with this ID to get its subject
                foreach ($user_messages as $msg) {
                    if ($msg['id'] == $message_id) {
                        $resp['message_subject'] = $msg['subject'];
                        break;
                    }
                }
            }
            
            // Add these responses to the main array
            $all_responses = array_merge($all_responses, $message_responses);
        }
    }
    
    // Sort responses by creation time (newest first)
    usort($all_responses, function($a, $b) {
        return strtotime($b['created']) - strtotime($a['created']);
    });
    
    // Return the result
    echo json_encode([
        'success' => true,
        'responses' => $all_responses,
        'debug' => [
            'message_count' => count($user_messages),
            'response_count' => count($all_responses),
            'user_email' => $user_email
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'responses' => []
    ]);
}
exit;
?> 