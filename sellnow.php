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

session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to add products.");
}

$user_id = $_SESSION['user_id'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form inputs
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $size = trim($_POST['size']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $image_filename = basename($_FILES['image_path']['name']); // Get image file name

    // Validate Category (Ensures it's correctly formatted)
    $valid_categories = ["Men", "Woman", "Men Accessories", "Woman Accessories"];
    if (!in_array($category, $valid_categories)) {
        die("Invalid category selected.");
    }

    // Define correct local storage path
    $target_directory = "C:/wamp64/www/ZestyWearSA/";
    $target_file = $target_directory . $image_filename;

    // Move uploaded image file to correct directory
    if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
        // Insert data into the database
        $sql = "INSERT INTO product (user_id, name, description, size, price, image_path, category) 
        VALUES ('$user_id', '$name', '$description', '$size', '$price', '$image_filename', 
        CASE 
        WHEN '$category' IN ('Men', 'Woman', 'Men Accessories', 'Woman Accessories') THEN '$category' 
        ELSE 'Unknown' 
        END)";



        // Execute query
        if ($conn->query($sql) === TRUE) {
            echo "Product successfully added!";
            
            // Redirect users to the correct category page
            if ($category === "Men") {
                header("Location: men.php");
            } elseif ($category === "Woman") {
                header("Location: woman.php");
            } elseif ($category === "Men Accessories") {
                header("Location: men_accessories.php");
            } elseif ($category === "Woman Accessories") {
                header("Location: woman_accessories.php");
            } else {
                header("Location: main.html"); // Default fallback
            }
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "File upload failed! Check folder permissions.";
    }
}

$conn->close();
?>