<?php
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
</body>
</html>
