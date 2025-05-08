<?php
class Response {
    // Database connection
    private $conn;
    private $table_name = "admin_responses";

    // Object properties
    public $id;
    public $message_id;
    public $admin_name;
    public $response_text;
    public $created;
    public $updated;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
        
        // Create the table if it doesn't exist
        $this->createTableIfNotExists();
    }
    
    // Create the admin_responses table if it doesn't exist
    private function createTableIfNotExists() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message_id INT NOT NULL,
            admin_name VARCHAR(100) NOT NULL,
            response_text TEXT NOT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (message_id) REFERENCES contact_messages(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    // Create a new response
    public function create() {
        // Insert query
        $query = "INSERT INTO " . $this->table_name . " 
                (message_id, admin_name, response_text, created) 
                VALUES (:message_id, :admin_name, :response_text, NOW())";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->message_id = htmlspecialchars(strip_tags($this->message_id));
        $this->admin_name = htmlspecialchars(strip_tags($this->admin_name));
        $this->response_text = htmlspecialchars(strip_tags($this->response_text));

        // Bind values
        $stmt->bindParam(":message_id", $this->message_id);
        $stmt->bindParam(":admin_name", $this->admin_name);
        $stmt->bindParam(":response_text", $this->response_text);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update a response
    public function update() {
        // Update query
        $query = "UPDATE " . $this->table_name . " 
                SET response_text = :response_text 
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->response_text = htmlspecialchars(strip_tags($this->response_text));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":response_text", $this->response_text);
        $stmt->bindParam(":id", $this->id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete a response
    public function delete() {
        // Delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind value
        $stmt->bindParam(":id", $this->id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get responses for a specific message
    public function getResponsesForMessage($message_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE message_id = :message_id 
                ORDER BY created ASC";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $message_id = htmlspecialchars(strip_tags($message_id));
        
        // Bind value
        $stmt->bindParam(":message_id", $message_id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get a single response by ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind value
        $stmt->bindParam(":id", $this->id);
        
        // Execute query
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->message_id = $row['message_id'];
            $this->admin_name = $row['admin_name'];
            $this->response_text = $row['response_text'];
            $this->created = $row['created'];
            $this->updated = $row['updated'];
            return true;
        }
        
        return false;
    }

    /**
     * Get responses after a specified date for a user's messages
     *
     * @param int $userId The user ID
     * @param string $date The date to check responses after
     * @return array The list of responses
     */
    public function getResponsesAfterDateForUser($userId, $date) {
        // Query to get all responses for messages belonging to this user
        // that were created after the specified date
        $query = "SELECT r.*, m.subject as message_subject 
                 FROM admin_responses r 
                 JOIN contact_messages m ON r.message_id = m.id 
                 WHERE m.user_id = :user_id 
                 AND r.created > :date
                 ORDER BY r.created DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getResponsesAfterDateForUser: " . $e->getMessage());
            return [];
        }
    }
}
?> 