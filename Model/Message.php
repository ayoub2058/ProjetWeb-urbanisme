<?php
class Message {
    // Connexion à la base de données
    private $conn;
    private $table_name = "contact_messages";

    // Propriétés de l'objet
    public $id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $status;
    public $created;

    // Constructeur avec $db comme connexion à la base de données
    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un message de contact
    public function create() {
        // Requête d'insertion
        $query = "INSERT INTO " . $this->table_name . " 
                (name, email, subject, message, status, created) 
                VALUES (:name, :email, :subject, :message, :status, NOW())";

        $stmt = $this->conn->prepare($query);

        // Nettoyer les données
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->subject = htmlspecialchars(strip_tags($this->subject));
        $this->message = htmlspecialchars(strip_tags($this->message));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Lier les valeurs
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":subject", $this->subject);
        $stmt->bindParam(":message", $this->message);
        $stmt->bindParam(":status", $this->status);

        // Exécuter la requête
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lire tous les messages
    public function read() {
        // Requête SELECT
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Mettre à jour le statut d'un message
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Récupérer les messages d'un utilisateur spécifique par email
    public function getUserMessages($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email ORDER BY created DESC";
        $stmt = $this->conn->prepare($query);
        
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(':email', $email);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lire un message spécifique
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id);
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mettre à jour un message
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET subject = :subject, message = :message, updated = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->subject = htmlspecialchars(strip_tags($this->subject));
        $this->message = htmlspecialchars(strip_tags($this->message));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Lier les valeurs
        $stmt->bindParam(':subject', $this->subject);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Supprimer un message
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?> 