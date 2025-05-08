<?php
require_once 'postC.php';
$postController = new PostC();

if (isset($_GET['id'])) {
    $postController->deletePost($_GET['id']);
    // Redirection après la suppression
    header("Location: index.php?success=2");
    exit;
}
