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
        function validateForm(event) {
            var titre = document.getElementById("titre").value;
            var description = document.getElementById("description").value;
            var image = document.getElementById("image").value;
            var mail = document.getElementById("mail").value;

            if (titre === "" || description === "" || image === "" || mail === "") {
                alert("Tous les champs doivent être remplis !");
                event.preventDefault();
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
</body>
</html> 