<?php
class Post {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPosts($sortBy = 'titre') {
        $sql = "SELECT * FROM posts ORDER BY " . $sortBy;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPost($id) {
        $sql = "SELECT * FROM posts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    


    public function deletePost($id) {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
} 