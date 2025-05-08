<?php
// Set error handling to suppress notices and warnings
error_reporting(E_ERROR);
ini_set('display_errors', 0);

// Prevent output buffering issues
ob_start();

// Start or resume session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and models
require_once '../../Config/Database.php';
require_once '../../Model/Response.php';

// Set content type to JSON
header('Content-Type: application/json');

// Default response
$response = [
    'success' => false,
    'new_responses' => 0,
    'responses' => [],
    'error' => null
];

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in");
    }
    
    $userId = $_SESSION['user_id'];
    
    // Get the last check time from query parameter
    $lastCheck = isset($_GET['last_check']) ? $_GET['last_check'] : null;
    
    if (!$lastCheck) {
        // Default to a day ago if not provided
        $lastCheck = date('Y-m-d H:i:s', strtotime('-1 day'));
    }
    
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    // Initialize Response model
    $responseModel = new Response($db);
    
    // Get new responses after last check time for user's messages
    $newResponses = $responseModel->getResponsesAfterDateForUser($userId, $lastCheck);
    
    // Process response
    $response['success'] = true;
    $response['new_responses'] = count($newResponses);
    $response['responses'] = $newResponses;
    
} catch (Exception $e) {
    $response['error'] = "An error occurred while checking for new responses";
}

// Clean output buffer to prevent any output before JSON
ob_end_clean();

// Send JSON response
echo json_encode($response);
exit;
?> 