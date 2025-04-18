<?php
session_start();

// Destroy the session
session_unset();
session_destroy();

// Clear local storage using JavaScript
echo "<script>
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('current_user');
    window.location.href = 'index.php';
</script>";
?>
