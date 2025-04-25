<?php
include_once __DIR__ . "/auth/config.php";
include_once __DIR__ . "/Post.php";

class PostC
{
    // Ajouter une publication
    public function addPost($titre, $description, $image)
    {
        $db = config::getConnexion();
        $query = $db->prepare("INSERT INTO post (titre, description, image) VALUES (:titre, :description, :image)");
        $query->bindParam(':titre', $titre);
        $query->bindParam(':description', $description);
        $query->bindParam(':image', $image);
        $query->execute();
    }

    // Modifier une publication
    public function updatePost($id, $titre, $description, $image)
    {
        $db = config::getConnexion();
        $query = $db->prepare("UPDATE post SET titre = :titre, description = :description, image = :image WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->bindParam(':titre', $titre);
        $query->bindParam(':description', $description);
        $query->bindParam(':image', $image);
        $query->execute();
    }

    // Supprimer une publication
    public function deletePost($id)
    {
        $db = config::getConnexion();
        $query = $db->prepare("DELETE FROM post WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();
    }

    // Récupérer une publication par ID
    public function getPost($id)
    {
        $db = config::getConnexion();
        $query = $db->prepare("SELECT * FROM post WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer toutes les publications
    public function listPosts()
    {
        $db = config::getConnexion();
        $query = $db->query("SELECT * FROM post ORDER BY id DESC");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Traiter le formulaire d'ajout de publication
    public function handleAddPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $imageName = $_FILES['image']['name'];
                $target = 'uploads/' . basename($imageName);
                move_uploaded_file($_FILES['image']['tmp_name'], $target);
            }
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $this->addPost($titre, $description, $imageName);
            header("Location: index.php?success=1");
        }
    }
}

// Gestion de la suppression
if (isset($_GET['delete'])) {
    $postController = new PostC();
    $postId = $_GET['delete'];
    $postController->deletePost($postId);
    header("Location: index.php"); // Rediriger après la suppression
    exit;
}

// Exécuter le traitement lors de la soumission du formulaire d'ajout de publication
$postController = new PostC();

?>
