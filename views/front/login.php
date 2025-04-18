<!-- filepath: c:\xampp1\htdocs\ghodwa\views\front\login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login / Register</title>
  <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.js"></script>
  <link rel="stylesheet" href="css/login.css" />
</head>
<body>
  <!-- Spline 3D Background -->
  <spline-viewer url="https://prod.spline.design/NOspby6AJwzuaFUg/scene.splinecode"></spline-viewer>

  <!-- Form Container -->
  <div class="container" id="form-container">
    <h2 id="form-title">welcome to clyptor</h2>
    <h2 id="form-title">Login</h2>

    <!-- Login Form -->
    <form id="login-form" method="POST" action="login.php">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="login">Login</button>
    </form>

    <!-- Register Form (Hidden by default) -->
    <form id="register-form" method="POST" action="register.php" style="display: none;">
      <input type="text" name="username" placeholder="Username" required />
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="register">Register</button>
    </form>

    <!-- Toggle Link -->
    <p style="text-align:center; margin-top: 20px; color: white;">
      <span id="toggle-text">Don't have an account?</span>
      <a href="#" id="toggle-link" style="color: #0ff;">Register</a>
    </p>
  </div>

  <!-- PHP Logic -->
  <?php
  session_start(); // Start the session

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
      try {
          // Database connection using PDO
          $dsn = 'mysql:host=localhost;dbname=clyptor;charset=utf8mb4';
          $pdo = new PDO($dsn, 'root', '', [
              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          ]);

          // Get form data
          $email = $_POST['email'];
          $password = $_POST['password'];

          // Query to check user credentials
          $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
          $stmt->execute(['email' => $email]);
          $user = $stmt->fetch();

          if ($user) {
              // Verify password
              if (password_verify($password, $user['password_hash'])) {
                  $_SESSION['user_id'] = $user['user_id'];
                  $_SESSION['username'] = $user['username'];

                  // Set localStorage values using JavaScript
                  echo "<script>
                      localStorage.setItem('isLoggedIn', 'true');
                      localStorage.setItem('current_user', JSON.stringify({
                          id: {$user['user_id']},
                          name: '{$user['username']}',
                          email: '{$user['email']}'
                      }));
                      window.location.href = '../front/index.php';
                  </script>";
                  exit();
              } else {
                  echo "<script>alert('Invalid email or password. Please try again.');</script>";
              }
          } else {
              echo "<script>alert('Invalid email or password. Please try again.');</script>";
          }
      } catch (PDOException $e) {
          echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
      }
  }
  ?>

  <!-- JavaScript -->
  <script>
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    const formTitle = document.getElementById("form-title");
    const toggleText = document.getElementById("toggle-text");
    const toggleLink = document.getElementById("toggle-link");

    toggleLink.addEventListener("click", (e) => {
      e.preventDefault();
      const isLogin = loginForm.style.display !== "none";

      if (isLogin) {
        loginForm.style.display = "none";
        registerForm.style.display = "block";
        formTitle.textContent = "Register";
        toggleText.textContent = "Already have an account?";
        toggleLink.textContent = "Login";
      } else {
        loginForm.style.display = "block";
        registerForm.style.display = "none";
        formTitle.textContent = "Login";
        toggleText.textContent = "Don't have an account?";
        toggleLink.textContent = "Register";
      }
    });
  </script>
</body>
</html>