<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "zestywearsa";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL
$id = $_GET['id'] ?? null;
if (!$id) { die("ID parameter missing!"); }

// Fetch product details (including size)
$sql = "SELECT id, name, description, price, image_path, size FROM product WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// Check if size data exists
$sizes = explode(",", $product['size']); // Splits sizes stored as "M,L,S,S" into an array
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> | Zesty Wear SA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="men_description.css">
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

    <div class="product-container">
      <div class = "left-image">
        <img src="http://localhost/ZestyWearSA/<?php echo htmlspecialchars($product['image_path']); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">

          <div class = "right-form">

        <div class="product-info">
            <h2><b><?php echo htmlspecialchars($product['name']); ?></b></h2>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <p class="price">R<?php echo htmlspecialchars($product['price']); ?></p>

            <!-- Dynamically Display Sizes -->
            <div class="size-selection">
                <?php foreach ($sizes as $size): ?>
                    <label>
                        <input type="radio" name="size" value="<?php echo htmlspecialchars(trim($size)); ?>">
                        <?php echo htmlspecialchars(trim($size)); ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <button class="add-to-cart">Add to Cart</button>
        </div>
    </div>
                </div>
                </div>

    <!--Footer of my website begins-->
     <footer class = "site-footer">
        <hr class = "footer-divider">
        <p class = "footer-text">© 2025 Zesty Wear SA | Privacy | Terms | Opt-Out Rights</p>
        <p class = "footer-text"> Created by: Anje Nieuwenhuis</p>
      </footer>
      <!--Footer ends-->
      
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

<?php $conn->close(); ?>
