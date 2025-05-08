<?php
require_once __DIR__ . '/../models/Post.php';

class PostController {
    private $postModel;

    public function __construct($db) {
        $this->postModel = new Post($db);
    }

    public function index($sortBy = 'titre') {
        $posts = $this->postModel->getAllPosts($sortBy);
        require_once __DIR__ . '/../views/posts/index.php';
    }

   

    


    
} 