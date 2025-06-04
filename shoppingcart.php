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

// Redirect if user is NOT logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_alert'] = true;
    header("Location: main.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql_user = "SELECT name FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc(); // Store user data


// Fetch user's cart items from the database
$cartItems = [];
$sql = "SELECT user_cart.quantity, user_cart.product_id, product.name, product.price, product.image_path 
        FROM user_cart 
        JOIN product ON user_cart.product_id = product.id 
        WHERE user_cart.user_id = ?";





$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

// Function to calculate total price
function calculateTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item["price"] * $item["quantity"];
    }
    return $total;
}

$totalPrice = calculateTotal($cartItems);

// Handle "Add to Cart" functionality
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-to-cart"])) {
    $product_id = $_POST["id"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $size = $_POST["size"];
    $image = $_POST["image"];

    // Store in session
    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }
  $_SESSION["cart"][] = [
    "id" => $product_id, // Use `product_id` instead of `id`
    "name" => $name,
    "price" => $price,
    "size" => $size,
    "image" => $image
];


    // Store in database (persistent cart)
    $sql = "INSERT INTO user_cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();


    header("Location: shoppingcart.php");
    exit();
}

// Handle "Remove from Cart" functionality
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove_item"])) {
    $product_id = $_POST["product_id"];
    $user_id = $_SESSION["user_id"];

    // Remove product from the database
    $sql = "DELETE FROM user_cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();

    // Refresh the page to update the cart display
    header("Location: shoppingcart.php");
    exit();
}


// Handle Checkout: Clear Cart & Redirect
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["checkout"])) {
    $sql = "DELETE FROM user_cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $_SESSION["cart"] = []; // Clear session cart

    header("Location: main.html"); // Redirect to main page
    exit();
}

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
    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
    <button type="submit" name="remove_item">Remove</button>
</form>


            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

           <!-- Display Total Price -->
<p>Total: <span id="cart-total">R<?php echo $totalPrice; ?></span></p>

<!-- Checkout Button -->
<form method="post" action="shoppingcart.php">
    <button type="submit" name="checkout">Pay Now</button>
</form>



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
