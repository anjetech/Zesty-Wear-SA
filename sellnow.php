<?php
// Database Connection
$host = "localhost";
$user = "root"; // Update if needed
$pass = ""; // Your DB password
$dbname = "zestywearsa";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $size = trim($_POST['size']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $image = $_FILES['image_path']['name']; // Get image file name

    // Validate Category (Ensures it's correctly formatted)
    $valid_categories = ["Men", "Women", "Men Accessories", "Women Accessories"];
    if (!in_array($category, $valid_categories)) {
        die("Invalid category selected.");
    }

    // Store only the filename in the database (not using an uploads/ folder)
    $image_filename = basename($_FILES['image_path']['name']);
    move_uploaded_file($_FILES["image_path"]["tmp_name"], "C:/wamp64/www/ZestyWearSA/" . $image_filename);

    // Insert data into the database
    $sql = "INSERT INTO product (name, description, size, price, image_path, category) 
            VALUES ('$name', '$description', '$size', '$price', '$image_filename', '$category')";

    // Execute query
    if ($conn->query($sql) === TRUE) {
        echo "Product successfully added!";
        
        // Redirect users to the correct category page
        if ($category === "Men") {
            header("Location: men.html");
        } elseif ($category === "Women") {
            header("Location: woman.html");
        } elseif ($category === "Men Accessories") {
            header("Location: men_accessories.html");
        } elseif ($category === "Women Accessories") {
            header("Location: woman_accessories.html");
        } else {
            header("Location: main.html"); // Default fallback
        }
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
