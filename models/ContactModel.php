<?php
class ContactModel {
    private $pdo;

    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=clyptor;charset=utf8mb4';
        $this->pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
        return $stmt->execute($data);
    }

    public function readAll() {
        $stmt = $this->pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM contact_messages WHERE message_id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE contact_messages SET name = :name, email = :email, subject = :subject, message = :message WHERE message_id = :id");
        $data['id'] = $id;
        return $stmt->execute($data);
    }
}
?>
