<?php
// Configuration de l'application
define('BASE_URL', 'http://localhost/clyptor'); // Remplacer par l'URL de votre application
define('APP_PATH', dirname(__FILE__));
define('DEBUG_MODE', true);

// Configuration des sessions
session_start();

// Configuration des chemins
define('VIEW_PATH', APP_PATH . '/View');
define('MODEL_PATH', APP_PATH . '/Model');
define('CONTROLLER_PATH', APP_PATH . '/Controller');

// Configuration des sous-domaines
define('FRONTOFFICE_URL', BASE_URL . '/View/frontoffice');
define('BACKOFFICE_URL', BASE_URL . '/View/backoffice');

// Configuration des assets
define('CSS_PATH', FRONTOFFICE_URL . '/css');
define('JS_PATH', FRONTOFFICE_URL . '/js');
define('IMAGES_PATH', FRONTOFFICE_URL . '/images');

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'clyptor_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Fonctions utilitaires
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Fonction pour charger automatiquement les classes
function autoload($class_name) {
    $paths = [MODEL_PATH, CONTROLLER_PATH];
    
    foreach($paths as $path) {
        $file = $path . '/' . $class_name . '.php';
        if(file_exists($file)) {
            require_once $file;
            return;
        }
    }
}

spl_autoload_register('autoload');
?> 