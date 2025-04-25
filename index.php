<?php
require_once 'postC.php';
$postController = new PostC();
$posts = $postController->listPosts();

// Vérifier si l'ajout ou la modification a réussi
if (isset($_GET['success']) && $_GET['success'] == '1') {
    echo "<script>alert('Annonce publiée avec succès!');</script>";
}

// Vérifier si on veut modifier un post
if (isset($_GET['edit'])) {
    $postId = $_GET['edit'];
    $post = $postController->getPost($postId); // Récupérer les données de l'annonce
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    // Télécharger l'image si elle est modifiée
    if ($image) {
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
    } else {
        // Si aucune nouvelle image, garder l'image actuelle
        $image = $post['image']; 
    }

    // Mettre à jour le post dans la base de données
    $postController->updatePost($id, $titre, $description, $image);

    // Rediriger après la modification
    header("Location: index.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clyptor - Home Rent</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home-rent.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Fonction de validation en JavaScript
        function validateForm(event) {
            // Empêcher l'envoi du formulaire si un champ est vide
            var titre = document.getElementById("titre").value;
            var description = document.getElementById("description").value;
            var image = document.getElementById("image").value;

            if (titre === "" || description === "" || image === "") {
                alert("Tous les champs doivent être remplis !");
                event.preventDefault();  // Empêche l'envoi du formulaire
            }
        }
    </script>
</head>
<body>

<header class="header">
    <div class="logo-container">
        <a href="index.php" class="logo"><span class="logo-text">Clyptor</span></a>
        <div class="logo-3d"></div>
    </div>
    <nav class="main-nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Carpooling</a></li>
            <li><a href="#">Home Rent</a></li>
            <li><a href="#">Car Rent</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>
    <div class="auth-buttons">
        <a href="#" class="btn btn-outline">Login</a>
        <a href="#" class="btn btn-primary">Register</a>
    </div>
    <button class="mobile-menu-toggle"><i class="fas fa-bars"></i></button>
</header>

<main>
    <section class="post-section">
        <h2>Poster une annonce de location</h2>
        <form class="post-form" method="post" action="index.php" enctype="multipart/form-data" onsubmit="validateForm(event)">
            <div class="form-group">
                <label for="id">ID de l'annonce</label>
                <input type="text" id="id" name="id" placeholder="Entrez l'ID de l'annonce" value="<?= isset($post) ? htmlspecialchars($post['id']) : '' ?>" readonly>
            </div>
            <div class="form-group">
                <label for="titre">Titre de l'annonce</label>
                <input type="text" id="titre" name="titre" placeholder="Ex: Maison avec jardin à louer" value="<?= isset($post) ? htmlspecialchars($post['titre']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" placeholder="Décrivez la maison..."><?= isset($post) ? htmlspecialchars($post['description']) : '' ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image de la maison</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" name="update" class="btn btn-primary"><?= isset($post) ? 'Modifier l\'annonce' : 'Publier l\'annonce' ?></button>
        </form>
    </section>

    <section id="posts-container" class="posts-container">
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <h3><?= htmlspecialchars($post['titre']) ?></h3>
                <p><?= nl2br(htmlspecialchars($post['description'])) ?></p>
                <?php if (!empty($post['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Image" style="max-width:100%; border-radius:10px;">
                <?php endif; ?>
                <a href="index.php?edit=<?= $post['id'] ?>" class="btn btn-outline">Modifier</a>
                <a href="postC.php?delete=<?= $post['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">Supprimer</a>
            </div>
        <?php endforeach; ?>
    </section>
</main>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section about">
            <a href="index.php" class="logo"><span class="logo-text">Clyptor</span></a>
            <p>Clyptor provides innovative solutions for carpooling, home rentals, and car rentals. Join our community today!</p>
            <div class="socials">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
        <div class="footer-section links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Carpooling</a></li>
                <li><a href="#">Home Rent</a></li>
                <li><a href="#">Car Rent</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
    </div>
</footer>

</body>
</html>
