<?php
// Direct Database Connection
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
    $name = $_POST['name'];
    $description = $_POST['description'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_FILES['image_path']['name']; // Get image file name

    // Upload image to 'uploads/' directory
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file);

    // Insert data into the database
    $sql = "INSERT INTO product (name, description, size, price, image_path, category) 
            VALUES ('$name', '$description', '$size', '$price', '$image', '$category')";

    // Execute query
    if ($conn->query($sql) === TRUE) {
        echo "Product successfully added!";
        header("Location: main.html"); // Redirect to main page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>