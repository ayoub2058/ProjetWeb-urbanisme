<?php
<<<<<<< HEAD
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

=======
session_start();

include_once 'Reservation.php';
include_once 'ReservationC.php';

$reservationC = new ReservationC();

// Messages de validation
$message = "";
if (isset($_GET['action'])) {
    if ($_GET['action'] === "ajout") {
        $message = "Réservation ajoutée avec succès.";
    } elseif ($_GET['action'] === "update") {
        $message = "Réservation modifiée avec succès.";
    } elseif ($_GET['action'] === "delete") {
        $message = "Réservation supprimée avec succès.";
    }
}

// Traitement de l'ajout
if (isset($_POST['submit'])) {
    $nom     = trim($_POST['nom']);
    $prenom  = trim($_POST['prenom']);
    $age     = trim($_POST['age']);
    $depuis  = trim($_POST['depuis']);
    $jusqua  = trim($_POST['jusqua']);

    $reservationC->addReservation($nom, $prenom, $age, $depuis, $jusqua);
    header("Location: index.php?action=ajout");
    exit();
}

// Traitement de la modification
if (isset($_POST['update'])) {
    $id      = trim($_POST['id']);
    $nom     = trim($_POST['nom']);
    $prenom  = trim($_POST['prenom']);
    $age     = trim($_POST['age']);
    $depuis  = trim($_POST['depuis']);
    $jusqua  = trim($_POST['jusqua']);

    $reservationC->updateReservation($id, $nom, $prenom, $age, $depuis, $jusqua);
    header("Location: index.php?action=update");
    exit();
}

// Suppression
if (isset($_GET['delete'])) {
    $reservationC->deleteReservation(trim($_GET['delete']));
    header("Location: index.php?action=delete");
    exit();
}

// Préparation modification
$reservationToEdit = null;
if (isset($_GET['edit'])) {
    $reservationData = $reservationC->getReservation(trim($_GET['edit']));
    if ($reservationData) {
        $reservationToEdit = $reservationData;
    }
}

$reservationList = $reservationC->listReservations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Réservations</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 20px; color: #333; }
        h1, h2 { text-align: center; color: #444; }
        form { background: #fff; padding: 20px; margin: 20px auto; border-radius: 8px;
               box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 500px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;
        }
        input[type="submit"], button {
            background: #28a745; border: none; color: #fff; padding: 10px 15px; margin-top: 15px;
            border-radius: 4px; cursor: pointer; transition: background 0.3s ease;
        }
        input[type="submit"]:hover, button:hover { background: #218838; }
        .reservation { background: #fff; padding: 15px; margin: 10px auto; border-radius: 8px;
                       box-shadow: 0 0 8px rgba(0,0,0,0.1); max-width: 600px; }
        .reservation p { margin: 5px 0; }
        .edit-link, .delete-link {
            text-decoration: none; font-weight: bold; margin-right: 10px; padding: 5px 10px;
            border-radius: 4px;
        }
        .edit-link { background: #007bff; color: #fff; }
        .edit-link:hover { background: #0069d9; }
        .delete-link { background: #dc3545; color: #fff; }
        .delete-link:hover { background: #c82333; }
    </style>
</head>
<body>
    <h1>Gestion des Réservations</h1>

    <?php if ($message): ?>
        <script>alert("<?= $message ?>");</script>
    <?php endif; ?>

    <?php if (!$reservationToEdit): ?>
        <h2>Ajouter une Réservation</h2>
        <form id="reservationForm" method="post" action="">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom">
            <label for="age">Âge :</label>
            <input type="number" id="age" name="age">
            <label for="depuis">Depuis :</label>
            <input type="date" id="depuis" name="depuis">
            <label for="jusqua">Jusqu'à :</label>
            <input type="date" id="jusqua" name="jusqua">
            <input type="submit" name="submit" value="Ajouter la Réservation">
        </form>
    <?php else: ?>
        <h2>Modifier la Réservation ID: <?= htmlspecialchars($reservationToEdit['id']) ?></h2>
        <form id="reservationForm" method="post" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($reservationToEdit['id']) ?>">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($reservationToEdit['nom']) ?>">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($reservationToEdit['prenom']) ?>">
            <label for="age">Âge :</label>
            <input type="number" id="age" name="age" value="<?= htmlspecialchars($reservationToEdit['age']) ?>">
            <label for="depuis">Depuis :</label>
            <input type="date" id="depuis" name="depuis" value="<?= htmlspecialchars($reservationToEdit['depuis']) ?>">
            <label for="jusqua">Jusqu'à :</label>
            <input type="date" id="jusqua" name="jusqua" value="<?= htmlspecialchars($reservationToEdit['jusqua']) ?>">
            <input type="submit" name="update" value="Mettre à jour la Réservation">
        </form>
    <?php endif; ?>

   

    <!-- JS de validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('reservationForm');

            if (form) {
                form.addEventListener('submit', function (e) {
                    const nom = form.nom.value.trim();
                    const prenom = form.prenom.value.trim();
                    const age = parseInt(form.age.value);
                    const depuis = form.depuis.value;
                    const jusqua = form.jusqua.value;

                    if (!nom || !prenom || !age || !depuis || !jusqua) {
                        alert("Veuillez remplir tous les champs.");
                        e.preventDefault();
                        return;
                    }

                    const nameRegex = /^[A-Za-zÀ-ÿ\s\-]+$/;
                    if (!nameRegex.test(nom) || !nameRegex.test(prenom)) {
                        alert("Le nom et le prénom doivent contenir uniquement des lettres.");
                        e.preventDefault();
                        return;
                    }

                    if (isNaN(age) || age < 18 || age > 120) {
                        alert("L'âge doit être un nombre entre 18 et 120.");
                        e.preventDefault();
                        return;
                    }

                    if (depuis >= jusqua) {
                        alert("La date de fin doit être après la date de début.");
                        e.preventDefault();
                        return;
                    }
                });
            }
        });
    </script>
>>>>>>> 92dbdcaea693e0b829a4416e3d025f55479d0d3c
</body>
</html>
