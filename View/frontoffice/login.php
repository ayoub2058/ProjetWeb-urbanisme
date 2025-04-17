<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Use absolute paths for includes
$root_path = realpath(dirname(__FILE__) . '/../../');
include_once $root_path . '/Controller/UserController.php';

// S'assurer que nous renvoyons toujours du JSON
header('Content-Type: application/json');

// Log errors to a file
function logError($message) {
    $logFile = __DIR__ . '/login_errors.log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Traiter la soumission du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userController = new UserController();
        
        // Récupérer les données du formulaire
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Log login attempt (without password)
        logError("Login attempt for user: " . $username);
        
        // Valider les données
        if (empty($username) || empty($password)) {
            $response = [
                'success' => false,
                'message' => 'Veuillez remplir tous les champs.'
            ];
        } else {
            // Tentative de connexion
            $result = $userController->login($username, $password);
            
            // Log login result (without sensitive data)
            logError("Login result: " . ($result['success'] ? 'success' : 'failure'));
            
            if ($result['success']) {
                // Connexion réussie
                $response = [
                    'success' => true,
                    'message' => 'Connexion réussie! Redirection...',
                    'redirect' => $result['is_admin'] ? '../backoffice/admin/admin.html' : '../../index.php'
                ];
            } else {
                // Échec de la connexion
                $response = [
                    'success' => false,
                    'message' => $result['message']
                ];
            }
        }
        
        // Renvoyer la réponse au format JSON
        echo json_encode($response);
    } catch (Exception $e) {
        // Log the error
        logError("Error: " . $e->getMessage());
        
        // En cas d'erreur, renvoyer un message d'erreur au format JSON
        echo json_encode([
            'success' => false, 
            'message' => 'Une erreur est survenue: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Si la méthode de requête n'est pas POST, renvoyer une erreur au format JSON
echo json_encode([
    'success' => false,
    'message' => 'Méthode non autorisée'
]);
exit;
?> 