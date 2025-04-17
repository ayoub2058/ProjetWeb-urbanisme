    </main>

    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>À propos de Clyptor</h5>
                    <p>Plateforme de services en ligne pour la location de voitures, de maisons et le covoiturage.</p>
                </div>
                <div class="col-md-4">
                    <h5>Liens rapides</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_URL; ?>" class="text-white">Accueil</a></li>
                        <li><a href="<?php echo BASE_URL; ?>?page=car" class="text-white">Location de voitures</a></li>
                        <li><a href="<?php echo BASE_URL; ?>?page=home" class="text-white">Location de maisons</a></li>
                        <li><a href="<?php echo BASE_URL; ?>?page=covoiturage" class="text-white">Covoiturage</a></li>
                        <li><a href="<?php echo BASE_URL; ?>?page=contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contactez-nous</h5>
                    <address>
                        <p><i class="fas fa-map-marker-alt me-2"></i> 123 Rue Exemple, Ville, Pays</p>
                        <p><i class="fas fa-phone me-2"></i> +123 456 7890</p>
                        <p><i class="fas fa-envelope me-2"></i> contact@clyptor.com</p>
                    </address>
                    <div class="social-icons">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> Clyptor. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo JS_PATH; ?>/main.js"></script>
</body>
</html> 