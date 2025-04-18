<?php
$dsn = 'mysql:host=localhost;dbname=clyptor;charset=utf8mb4';
$pdo = new PDO($dsn, 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, role = :role WHERE user_id = :user_id");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'role' => $role,
        'user_id' => $user_id,
    ]);

    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    <form method="POST" action="edit_user.php">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <input type="text" name="role" value="<?= htmlspecialchars($user['role']) ?>" required>
        <button type="submit" name="update">Update</button>
    </form>
</body>
</html>
