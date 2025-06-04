<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "zestywearsa";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database (Make sure "Men Accessories" matches database entries)
$sql = "SELECT id, name, description, price, image_path FROM product WHERE category='Men Accessories'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Men Accessories | Zesty Wear SA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="men_accessories.css">
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

<h2 class="men-accessories-heading">Men's Accessories Collection</h2>

<div class="product-grid">
    <?php 
    $counter = 0; // Track how many items per row  
    while ($row = $result->fetch_assoc()) { 
        if ($counter % 4 == 0) { echo "<div class='product-row'>"; } // Start a new row every 4 items
    ?>
        <div class="product-card">
            <a href="men_acc_description.php?id=<?php echo $row['id']; ?>" class="image-link">
                <img src="http://localhost/ZestyWearSA/<?php echo htmlspecialchars($row['image_path']); ?>" class="product-image">
            </a>
            <h3 class="product-name"><?php echo htmlspecialchars($row['name']); ?></h3>
            <p class="product-price">R<?php echo htmlspecialchars($row['price']); ?></p>
        </div>
    <?php 
        $counter++;
        if ($counter % 4 == 0) { echo "</div>"; } // Close the row after every 4 items
    } 
    if ($counter % 4 != 0) { echo "</div>"; } // Ensure closing the last row properly
    ?>
</div>


<!-- Footer -->
<footer class="site-footer">
    <hr class="footer-divider">
    <p class="footer-text">© 2025 Zesty Wear SA | Privacy | Terms | Opt-Out Rights</p>
    <p class="footer-text"> Created by: Anje Nieuwenhuis</p>
</footer>

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

<script src="searchbar.js"></script>

</body>
</html>

<?php
$conn->close();
?>
