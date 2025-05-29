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
    <link rel="stylesheet" href="profile.css">
    <title>Profile | Zesty Wear SA</title>
    <style>
        <?php include 'profile.css'; ?>
    </style>
</head>
<body>
<!-- Navbar begins-->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
          
          <a href="main.html">
            <h1 class="header-name">Zesty Wear SA</h1>
        </a>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

              

              <li class="nav-item">
                <a href = "link00" class = "nav-link"><small> Chart</small></a>
              </li>

              <li class="nav-item">
                <a href = "link00" class = "nav-link"><small>Sell Now</small></a>
              </li>

              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <small>Categories </small>
                </a>

                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="men.html">Men</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="woman.html">Woman</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="men_accessories.html">Men Accessories</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="woman_accessories.html">Woman Accessories</a></li>
                  <li><hr class="dropdown-divider"></li>
                </ul>
              </li>
            </ul>

            <form id="searchForm" class="d-flex" role="search">
              <input id="searchInput" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
              <button class="btn btn-outline-success" type="submit">Search</button>
          </form>
          
            <!-- Sign in/login links-->
             <div class = "auth-links">
              <a href = "login.html" class = "login-link">Log in</a>
              <a href = "signup.html" class = "signup-link">Register</a>
              <a href = "profile.php" class = "profile-link">Profile</a>
             </div>

          </div>
        </div>
        <hr>
      </nav>
<!-- Navbar ends-->

<!-- Profile Section -->
<div class="container mt-4">
    <h1><b>Welcome, <?php echo htmlspecialchars($user['name']); ?></b></h1>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <h2>Your Listings:</h2>
    
    <form method="post">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>
</div>

<!-- Footer -->
<footer class="site-footer">
    <hr class="footer-divider">
    <p class="footer-text">© 2025 Zesty Wear SA | Privacy | Terms | Opt-Out Rights</p>
    <p class="footer-text">Created by: Anje Nieuwenhuis</p>
</footer>


  <!--Profile alert-->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
    fetch('check_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.showAlert) {
                alert("You need to log in to access your profile!");
            }
        });
    });
</script>

    <!--Linking my javascript file-->
    <script src="searchbar.js"></script>

</body>
</html>
