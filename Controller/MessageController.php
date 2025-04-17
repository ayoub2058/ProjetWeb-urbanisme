<?php
// Inclure les fichiers nécessaires
include_once '../Model/Database.php';
include_once '../Model/Message.php';

class MessageController {
    private $message;
    
    public function __construct() {
        // Obtenir la connexion à la base de données
        $database = new Database();
        $db = $database->getConnection();
        
        // Initialiser l'objet Message
        $this->message = new Message($db);
    }
    
    // Enregistrer un nouveau message de contact
    public function saveMessage($name, $email, $subject, $messageText) {
        if(empty($name) || empty($email) || empty($subject) || empty($messageText)) {
            return ["success" => false, "message" => "Tous les champs sont requis"];
        }
        
        // Valider l'email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "Email invalide"];
        }
        
        // Définir les valeurs du message
        $this->message->name = $name;
        $this->message->email = $email;
        $this->message->subject = $subject;
        $this->message->message = $messageText;
        $this->message->status = "Nouveau";
        
        // Créer le message
        if($this->message->create()) {
            return ["success" => true, "message" => "Message envoyé avec succès"];
        } else {
            return ["success" => false, "message" => "Impossible d'envoyer le message"];
        }
    }
    
    // Récupérer tous les messages
    public function getAllMessages() {
        $stmt = $this->message->read();
        $messages = [];
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $message_item = [
                "id" => $id,
                "name" => $name,
                "email" => $email,
                "subject" => $subject,
                "message" => $message,
                "status" => $status,
                "created" => $created
            ];
            array_push($messages, $message_item);
        }
        
        return $messages;
    }
    
    // Mettre à jour le statut d'un message
    public function updateMessageStatus($id, $status) {
        $this->message->id = $id;
        $this->message->status = $status;
        
        if($this->message->updateStatus()) {
            return ["success" => true, "message" => "Statut mis à jour avec succès"];
        } else {
            return ["success" => false, "message" => "Impossible de mettre à jour le statut"];
        }
    }
}
?> 