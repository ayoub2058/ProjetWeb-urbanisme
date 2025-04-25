<?php
require_once 'postC.php';
$postController = new PostC();

if (isset($_GET['id'])) {
    $postController->deletePost($_GET['id']);
}

header("Location: index.php");
