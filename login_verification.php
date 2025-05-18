<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
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

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $email = $conn->real_escape_string($email);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $stored_password);
        $stmt->fetch();

        // Simple password comparison (not hashed)
        if ($password === $stored_password) {
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            header("Location: main.html");
            exit();
        } else {
            // Redirect with error message
            header("Location: login.html?error=" . urlencode("Invalid password. Please try again."));
            exit();
        }
    } else {
        // Redirect with error message
        header("Location: login.html?error=" . urlencode("No account found with this email."));
        exit();
    }

    $stmt->close();
}

$conn->close();
exit();
?>
