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
    $_SESSION['login_alert'] = true;
    header("Location: main.html");
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

// Fetch user's product listings
$product_sql = "SELECT id, name, description, size, price, image_path, category FROM product WHERE user_id = ?";
$product_stmt = $conn->prepare($product_sql);
$product_stmt->bind_param("i", $user_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Zesty Wear SA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="profile.css">
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
                <a href = "chart.html" class = "nav-link"><small> Chart</small></a>
              </li>

              <li class="nav-item">
                <a href = "sellnow.html" class = "nav-link"><small>Sell Now</small></a>
              </li>

              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <small>Categories </small>
                </a>

                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="men.php">Men</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="woman.php">Woman</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="men_accessories.php">Men Accessories</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="woman_accessories.php">Woman Accessories</a></li>
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
      </nav>
<!-- Navbar ends-->

<!-- Profile Section -->
<div class="container mt-4">
    <h1><b>Welcome, <?php echo htmlspecialchars($user['name']); ?></b></h1>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <h2>Your Listings:</h2>

    <?php if ($product_result->num_rows > 0): ?>
        <div class="row">
            <?php while ($product = $product_result->fetch_assoc()): ?>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="card-text"><strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?></p>
                            <p class="card-text"><strong>Price:</strong> R<?php echo htmlspecialchars($product['price']); ?></p>
                            <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>

                            <?php
                                $category = strtolower(trim($product['category']));
                                $categoryPage = "main.html"; // fallback

                                switch ($category) {
                                    case 'woman':
                                        $categoryPage = "woman.php";
                                        break;
                                    case 'men':
                                        $categoryPage = "men.php";
                                        break;
                                    case 'woman accessories':
                                        $categoryPage = "woman_accessories.php";
                                        break;
                                    case 'men accessories':
                                        $categoryPage = "men_accessories.php";
                                        break;
                                }
                            ?>

                            <a href="<?php echo $categoryPage; ?>" class="btn btn-primary mt-2">View Product</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>You have no listings yet.</p>
    <?php endif; ?>

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

<!-- Profile alert -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetch('check_session.php')
            .then(response => response.json())
            .then(data => {
                if (data.showAlert) {
                    alert("You need to log in to access your profile!");
                }
            });
    });
</script>

<script src="searchbar.js"></script>
</body>
</html>
