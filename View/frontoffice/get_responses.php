<?php
// Include necessary files
require_once '../../config.php';
require_once MODEL_PATH . '/Database.php';
require_once MODEL_PATH . '/Response.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Check if message_id is provided
if (!isset($_GET['message_id']) || empty($_GET['message_id'])) {
    echo json_encode([
        'error' => 'Message ID is required'
    ]);
    exit;
}

$message_id = (int)$_GET['message_id'];

// Initialize database and response objects
$database = new Database();
$db = $database->getConnection();
$response = new Response($db);

// Get responses for this message
$responses = $response->getResponsesForMessage($message_id);

// Return the responses as JSON
echo json_encode($responses);
exit;
?> 