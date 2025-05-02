<?php
session_start();

$fbAppId = 'TON_APP_ID'; // Remplace par ton ID d'application Facebook
$fbAppSecret = 'TON_APP_SECRET'; // Remplace par ton secret d'application Facebook
$redirectUri = 'http://localhost/wahdi/facebook_callback.php'; // URL de callback

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Échange le code d'autorisation contre un access token
    $token_url = "https://graph.facebook.com/v11.0/oauth/access_token?client_id={$fbAppId}&redirect_uri={$redirectUri}&client_secret={$fbAppSecret}&code={$code}";
    $response = file_get_contents($token_url);
    $params = json_decode($response, true);

    if (isset($params['access_token'])) {
        $accessToken = $params['access_token'];

        // Récupère les informations de l'utilisateur Facebook
        $user_info = file_get_contents("https://graph.facebook.com/me?fields=id,name,email&access_token={$accessToken}");
        $user = json_decode($user_info, true);

        // Vérifie si l'email est récupéré et connecte ou enregistre l'utilisateur
        if (isset($user['email'])) {
            try {
                $pdo = new PDO('mysql:host=localhost;dbname=clyptorweb;charset=utf8mb4', 'root', '', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);

                // Recherche l'utilisateur dans la base de données
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$user['email']]);
                $existingUser = $stmt->fetch();

                if ($existingUser) {
                    $_SESSION['user_id'] = $existingUser['id'];
                    $_SESSION['username'] = $existingUser['username'];
                } else {
                    // Enregistre l'utilisateur dans la base de données
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, '')");
                    $stmt->execute([$user['name'], $user['email']]);
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['username'] = $user['name'];
                }

                // Redirige vers la page d'accueil
                header('Location: index.php');
                exit();

            } catch (PDOException $e) {
                echo "Erreur DB : " . $e->getMessage();
            }
        }
    }
}
?>
