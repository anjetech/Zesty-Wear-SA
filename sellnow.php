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

// Handle Product Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Check received data
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";
    exit; // Stop execution to check received values before proceeding

    // Check if required fields exist
    if (empty($_POST["name"]) || empty($_POST["category"]) || empty($_POST["price"]) || empty($_FILES["image_path"]["name"])) {
        echo "Error: Missing required fields.";
        exit;
    }

    $name = $_POST["name"];
    $category = $_POST["category"];
    $description = $_POST["description"] ?? ""; // Optional
    $price = $_POST["price"];
    $size = $_POST["size"] ?? ""; // Optional
    $stock = 1; // Default stock value

    // Ensure an image file was uploaded correctly
    if (!isset($_FILES["image_path"]["name"]) || empty($_FILES["image_path"]["name"])) {
        echo "Error: No image uploaded.";
        exit;
    }

    // Image Upload Handling
    $target_dir = "uploads/";
    $image_name = basename($_FILES["image_path"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;
    $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validation
    if (!is_numeric($price)) {
        echo "Price must be a valid number.";
        exit;
    }

    if (!in_array($image_type, ["jpg", "jpeg", "png"])) {
        echo "Only JPG, JPEG, and PNG files are allowed.";
        exit;
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO product (name, category, description, price, size, stock, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssis", $name, $category, $description, $price, $size, $stock, $target_file);

        if ($stmt->execute()) {
            // Redirect to correct category page
            switch ($category) {
                case "Men":
                    header("Location: men.html");
                    exit;
                case "Women":
                    header("Location: woman.html");
                    exit;
                case "Accessories":
                    header("Location: accessories.html");
                    exit;
                default:
                    echo "Invalid category.";
                    exit;
            }
        } else {
            echo "Error uploading product.";
        }
        $stmt->close();
    } else {
        echo "Failed to upload image.";
    }

    $conn->close();
}
?>
