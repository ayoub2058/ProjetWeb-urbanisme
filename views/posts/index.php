<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main>
   

    <section id="posts-container" class="posts-container">
        <!-- Formulaire de tri -->
        <form method="get" action="index.php">
            <input type="hidden" name="action" value="index">
            <label for="sort">Trier par :</label>
            <select name="sort" id="sort">
                <option value="titre" <?= $sortBy == 'titre' ? 'selected' : '' ?>>Titre</option>
            </select>
            <button type="submit" class="btn btn-primary">Trier</button>
        </form>

        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <h3><?= htmlspecialchars($post['titre']) ?></h3>
                <p><?= nl2br(htmlspecialchars($post['description'])) ?></p>
                <?php if (!empty($post['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Image" style="max-width:100%; border-radius:10px;">
                <?php endif; ?>
                <p><strong>Email :</strong> <?= htmlspecialchars($post['mail']) ?></p>
              
      
                <?php if ($post['status'] == 'disponible'): ?>
                    <a href="http://localhost/gestion_reservation/index.php"  class="btn btn-primary">Réserver</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 