<?php
// Start the session to access session variables
session_start();

// Database connection credentials
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "zestywearsa";

// Create a connection using MySQLi
$conn = new mysqli($host, $user, $pass, $dbname);

// Check for any connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if product ID is provided via GET and user is logged in
if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $id = $_GET['id'];                 // Get product ID from URL
    $user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

    // Prepare a SQL query to fetch the product from the database
    $stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
    $stmt->bind_param("i", $id); // Bind the product ID as an integer
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc(); // Retrieve the product data as an associative array

    if ($product) {
        // Check if the product is already in the user's cart
        $check = $conn->prepare("SELECT quantity FROM user_cart WHERE user_id = ? AND product_id = ?");
        $check->bind_param("ii", $user_id, $id);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            // Product exists in cart — update quantity
            $existing = $checkResult->fetch_assoc();
            $newQuantity = $existing['quantity'] + 1;

            $update = $conn->prepare("UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $update->bind_param("iii", $newQuantity, $user_id, $id);
            $update->execute();
        } else {
            // Product does not exist in cart — insert new row with quantity = 1
            $insert = $conn->prepare("INSERT INTO user_cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $insert->bind_param("ii", $user_id, $id);
            $insert->execute();
        }

        // Redirect to shopping cart page after processing
        header("Location: shoppingcart.php");
        exit;
    } else {
        // Product not found in the database
        echo "Product not found.";
    }
} else {
    echo "<script>alert('You need to login first before you can add products to the shopping cart.'); window.location.href='login.html';</script>";

    /* Required data (product ID or user login) missing
    echo "Missing product ID or user is not logged in.";*/
}
?>