<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zestywearsa";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If user is NOT logged in, redirect to main page
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_alert'] = true;
    header("Location: main.html");
    exit();
}

// Handle "Add to Cart" functionality
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-to-cart"])) {
    $product = [
        "id" => $_POST["id"],
        "name" => $_POST["name"],
        "description" => $_POST["description"],
        "price" => $_POST["price"],
        "image" => $_POST["image"],
        "size" => $_POST["size"]
    ];
    
    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }

    $_SESSION["cart"][] = $product;

    header("Location: shoppingcart.php");
    exit();
}

// Handle "Remove from Cart" functionality
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove_item"])) {
    $index = $_POST["index"];
    unset($_SESSION["cart"][$index]);

    // Re-index the array to prevent gaps in indexes
    $_SESSION["cart"] = array_values($_SESSION["cart"]);

    header("Location: shoppingcart.php");
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
    <title>Profile | Zesty Wear SA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="shoppingcart.css">
    <style>
        <?php include 'shoppingcart.css'; ?>
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
                <a href = "shoppingcart.php" class = "nav-link"><small>Shopping Cart</small></a>
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

<!-- Shopping cart Section -->
 <div class="container mt-4">
 <h1><b>Welcome to the Shopping Cart, <?php echo htmlspecialchars($user['name']); ?></b></h1>
    <h2>Your Products:</h2>
    <hr class="product-divider">

    <?php if (!isset($_SESSION["cart"]) || count($_SESSION["cart"]) === 0): ?>
        <p>Your shopping cart is empty.</p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($_SESSION["cart"] as $index => $item): ?>
                <div class="cart-card">
                    <img src="http://localhost/ZestyWearSA/<?php echo htmlspecialchars($item["image"]); ?>" class="cart-image">
                    <h3><?php echo htmlspecialchars($item["name"]); ?></h3>
                    <p>Size: <?php echo htmlspecialchars($item["size"]); ?></p>
                    <p>Price: R<?php echo htmlspecialchars($item["price"]); ?></p>
                    
                    <form method="post" action="shoppingcart.php">
                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                    <button type="submit" name="remove_item">Remove</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
// Prevent form resubmission on refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

<!-- Footer -->
<footer class="site-footer">
    <hr class="footer-divider">
    <p class="footer-text">© 2025 Zesty Wear SA | Privacy | Terms | Opt-Out Rights</p>
    <p class="footer-text">Created by: Anje Nieuwenhuis</p>
</footer>

<!-- Shopping cart alert -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetch('check_session.php')
            .then(response => response.json())
            .then(data => {
                if (data.showAlert) {
                    alert("You need to log in to access your shopping cart!");
                }
            });
    });
</script>

<script src="searchbar.js"></script>
</body>
</html>
