<?php
// Inclure les fichiers nécessaires
$root_path = realpath(dirname(__FILE__) . '/..');
include_once $root_path . '/Model/Database.php';
include_once $root_path . '/Model/User.php';

class UserController {
    public $user;
    
    public function __construct() {
        // Obtenir la connexion à la base de données
        $database = new Database();
        $db = $database->getConnection();
        
        // Initialiser l'objet User
        $this->user = new User($db);
    }
    
    // Gérer l'inscription d'un utilisateur
    public function register($username, $password, $email) {
        if(empty($username) || empty($password) || empty($email)) {
            return ["success" => false, "message" => "Tous les champs sont requis"];
        }
        
        // Valider l'email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "Email invalide"];
        }
        
        // Vérifier si l'utilisateur existe déjà
        if($this->user->userExists($username, $email)) {
            return ["success" => false, "message" => "Nom d'utilisateur ou email déjà utilisé"];
        }
        
        // Définir les valeurs de l'utilisateur
        $this->user->username = $username;
        $this->user->password = $password;
        $this->user->email = $email;
        
        // Créer l'utilisateur
        if($this->user->create()) {
            return ["success" => true, "message" => "Utilisateur créé avec succès"];
        } else {
            return ["success" => false, "message" => "Impossible de créer l'utilisateur"];
        }
    }
    
    // Gérer l'authentification d'un utilisateur
    public function login($username, $password) {
        if(empty($username) || empty($password)) {
            return ["success" => false, "message" => "Nom d'utilisateur et mot de passe requis"];
        }
        
        // Authentifier l'utilisateur
        if($this->user->login($username, $password)) {
            // Démarrer la session si ce n'est pas déjà fait
            if(session_status() == PHP_SESSION_NONE && !headers_sent()) {
                session_start();
            }
            
            // Stocker les données de l'utilisateur en session
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['username'] = $this->user->username;
            $_SESSION['email'] = $this->user->email;
            $_SESSION['is_admin'] = $this->user->is_admin;
            $_SESSION['logged_in'] = true;
            
            return [
                "success" => true, 
                "message" => "Connexion réussie",
                "user_id" => $this->user->id,
                "username" => $this->user->username,
                "email" => $this->user->email,
                "is_admin" => $this->user->is_admin
            ];
        } else {
            return ["success" => false, "message" => "Nom d'utilisateur ou mot de passe incorrect"];
        }
    }
    
    // Déconnexion
    public function logout() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Détruire toutes les variables de session
        $_SESSION = [];
        
        // Détruire la session
        session_destroy();
        
        return ["success" => true, "message" => "Déconnexion réussie"];
    }
    
    // Récupérer tous les utilisateurs
    public function getAllUsers() {
        $stmt = $this->user->read();
        $users = [];
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $user_item = [
                "id" => $id,
                "username" => $username,
                "email" => $email,
                "created" => $created
            ];
            array_push($users, $user_item);
        }
        
        return $users;
    }
    
    // Récupérer les informations d'un utilisateur
    public function getUserInfo($id) {
        $this->user->readOne($id);
        
        if($this->user->username != null) {
            return [
                "id" => $this->user->id,
                "username" => $this->user->username,
                "email" => $this->user->email,
                "is_admin" => $this->user->is_admin,
                "created" => $this->user->created
            ];
        }
        
        return ["success" => false, "message" => "Utilisateur non trouvé"];
    }
    
    // Mettre à jour les informations d'un utilisateur
    public function updateUser($id, $username, $email) {
        $this->user->id = $id;
        $this->user->username = $username;
        $this->user->email = $email;
        
        if($this->user->update()) {
            // Mettre à jour les informations de session
            if(session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
            }
            
            return ["success" => true, "message" => "Informations mises à jour avec succès"];
        }
        
        return ["success" => false, "message" => "Impossible de mettre à jour les informations"];
    }
    
    // Changer le mot de passe
    public function changePassword($id, $current_password, $new_password) {
        // Vérifier d'abord que l'utilisateur existe
        if(!$this->user->readOne($id)) {
            return ["success" => false, "message" => "Utilisateur non trouvé"];
        }
        
        // Vérifier l'ancien mot de passe
        if(!$this->user->login($this->user->username, $current_password)) {
            return ["success" => false, "message" => "Mot de passe actuel incorrect"];
        }
        
        // Mettre à jour le mot de passe
        if($this->user->updatePassword($new_password)) {
            return ["success" => true, "message" => "Mot de passe changé avec succès"];
        }
        
        return ["success" => false, "message" => "Impossible de changer le mot de passe"];
    }
    
    // Vérifier si l'utilisateur est connecté
    public function isLoggedIn() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    // Vérifier si l'utilisateur est un administrateur
    public function isAdmin() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === 1;
    }
}

// Gérer les requêtes API
if(isset($_GET['action'])) {
    $controller = new UserController();
    $action = $_GET['action'];
    
    // Démarrer la session si nécessaire
    if(session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    switch($action) {
        case 'register':
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = isset($_POST['username']) ? $_POST['username'] : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';
                $email = isset($_POST['email']) ? $_POST['email'] : '';
                
                $result = $controller->register($username, $password, $email);
                
                if($result['success']) {
                    // Rediriger vers la page de connexion
                    $_SESSION['message'] = $result['message'];
                    header('Location: ../index.php?page=user&action=login');
                } else {
                    // Rediriger vers la page d'inscription avec un message d'erreur
                    $_SESSION['error'] = $result['message'];
                    header('Location: ../index.php?page=user&action=register');
                }
                exit;
            }
            break;
            
        case 'login':
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = isset($_POST['username']) ? $_POST['username'] : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';
                
                $result = $controller->login($username, $password);
                
                if($result['success']) {
                    // Rediriger vers la page d'accueil ou le tableau de bord
                    $_SESSION['message'] = "Bienvenue, " . $result['username'] . "!";
                    
                    if($result['is_admin']) {
                        header('Location: ../View/backoffice/admin/admin.html');
                    } else {
                        header('Location: ../index.php');
                    }
                } else {
                    // Rediriger vers la page de connexion avec un message d'erreur
                    $_SESSION['error'] = $result['message'];
                    header('Location: ../index.php?page=user&action=login');
                }
                exit;
            }
            break;
            
        case 'logout':
            $controller->logout();
            $_SESSION['message'] = "Vous avez été déconnecté avec succès.";
            header('Location: ../index.php');
            exit;
            break;
            
        case 'profile':
            if(!$controller->isLoggedIn()) {
                $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
                header('Location: ../index.php?page=user&action=login');
                exit;
            }
            
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
                $id = $_SESSION['user_id'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                
                $result = $controller->updateUser($id, $username, $email);
                
                if($result['success']) {
                    $_SESSION['message'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }
                
                header('Location: ../index.php?page=user&action=profile');
                exit;
            }
            
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
                $id = $_SESSION['user_id'];
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                
                if($new_password !== $confirm_password) {
                    $_SESSION['error'] = "Les nouveaux mots de passe ne correspondent pas.";
                    header('Location: ../index.php?page=user&action=profile');
                    exit;
                }
                
                $result = $controller->changePassword($id, $current_password, $new_password);
                
                if($result['success']) {
                    $_SESSION['message'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }
                
                header('Location: ../index.php?page=user&action=profile');
                exit;
            }
            break;
            
        default:
            header('Location: ../index.php');
            exit;
    }
}
?> 