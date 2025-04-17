<?php
class User {
    // Connexion à la base de données
    private $conn;
    private $table_name = "users";

    // Propriétés de l'objet
    public $id;
    public $username;
    public $password;
    public $email;
    public $is_admin;
    public $created;

    // Constructeur avec $db comme connexion à la base de données
    public function __construct($db) {
        $this->conn = $db;
    }

    // Méthode pour lire tous les utilisateurs
    public function read() {
        // Requête SELECT
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Créer un utilisateur
    public function create() {
        // Requête d'insertion
        $query = "INSERT INTO " . $this->table_name . " 
                (username, password, email, created) 
                VALUES (:username, :password, :email, NOW())";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Lier les valeurs
        $stmt->bindParam(":username", $this->username);

        // Hasher le mot de passe
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);
        
        $stmt->bindParam(":email", $this->email);

        // Exécuter la requête
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Méthode pour connecter un utilisateur
    public function login($username, $password) {
        // Requête pour trouver l'utilisateur
        $query = "SELECT id, username, password, email, is_admin FROM " . $this->table_name . " WHERE username = :username OR email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $username = htmlspecialchars(strip_tags($username));
        
        // Lier les paramètres
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $username); // Permet de se connecter avec l'email ou le nom d'utilisateur
        
        $stmt->execute();
        
        // Vérifier si l'utilisateur existe
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier le mot de passe
            if(password_verify($password, $row['password'])) {
                // Stocker les données de l'utilisateur
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->is_admin = $row['is_admin'];
                
                return true;
            }
        }
        
        return false;
    }
    
    // Méthode pour vérifier si un utilisateur existe
    public function userExists($username, $email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username OR email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $username = htmlspecialchars(strip_tags($username));
        $email = htmlspecialchars(strip_tags($email));
        
        // Lier les paramètres
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Récupérer un utilisateur par son ID
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->is_admin = $row['is_admin'];
            $this->created = $row['created'];
            
            return true;
        }
        
        return false;
    }
    
    // Mettre à jour un utilisateur
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                username = :username, 
                email = :email
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Lier les paramètres
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
    
    // Mettre à jour le mot de passe
    public function updatePassword($new_password) {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Hasher le nouveau mot de passe
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        
        // Lier les paramètres
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
}
?> 