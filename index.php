<?php
require_once 'config/database.php';
require_once 'controllers/PostController.php';

// Initialiser la connexion à la base de données
$database = Database::getInstance();
$db = $database->getConnection();

// Initialiser le contrôleur
$postController = new PostController($db);

// Router les requêtes
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'titre';

switch ($action) {
    case 'index':
        $postController->index($sortBy);
        break;
    
    
   
   
    default:
        $postController->index($sortBy);
        break;
}
