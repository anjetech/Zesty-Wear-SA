<?php
session_start();

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zestywearsa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<script>alert('Connection failed: " . $conn->connect_error . "');</script>");
}

// Handle logout action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    $_SESSION = [];
    session_destroy();
    
    echo "<script>
            alert('You have been logged out successfully.');
            window.location.href='main.html';
          </script>";
    exit();
}

// If user is NOT logged in, set session flag instead of redirecting immediately
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_alert'] = true; // Set alert flag
    header("Location: main.html"); // Redirect user without showing alert in profile.php
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    
    <title>Profile | Zesty Wear SA</title>
    <style>
        <?php include 'profile.css'; ?>
    </style>
</head>
<body>

<!-- Profile Section -->
<div class="container mt-4">
    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <h3>Your Listings:</h3>
    
    <form method="post">
        <button type="submit" name="logout" class="btn btn-danger">Logout</button>
    </form>
</div>

<!-- Footer -->
<footer class="site-footer">
    <hr class="footer-divider">
    <p class="footer-text">© 2025 Zesty Wear SA | Privacy | Terms | Opt-Out Rights</p>
    <p class="footer-text">Created by: Anje Nieuwenhuis</p>
</footer>

</body>
</html>
