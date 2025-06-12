<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zestywearsa";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Location: signup.html?error_name=" . urlencode("Database connection failed."));
    exit();
}

$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']); 
    $email = trim($_POST['email']); 
    $password = trim($_POST['password']); 
    $confirm_password = trim($_POST['confirm_password']); 

    $errors = [];

    // Validate name
    if (empty($name)) {
        $errors['error_name'] = "Name field is required.";
    }

    // Validate email
    if (empty($email)) {
        $errors['error_email'] = "Email field is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['error_email'] = "Invalid email format.";
    }

    // Validate password
    if (empty($password)) {
        $errors['error_password'] = "Password field is empty!";
    }

    // Validate confirm password
    if ($password !== $confirm_password) {
        $errors['error_confirm_password'] = "Passwords do not match.";
    }

    // Check if email already exists only if email format is valid and no email error yet
    if (!isset($errors['error_email'])) {
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $errors['error_email'] = "There is already an email address for that email. Please login.";
        }
        $check_email->close();
    }

    // If there are errors, redirect back with all errors
    if (!empty($errors)) {
        $query = http_build_query($errors);
        header("Location: signup.html?$query");
        exit();
    }

    // Insert user (consider hashing password)
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['email'] = $email;

    // Redirect to signup.html with success flag
    header("Location: signup.html?success=1");
        exit();
    } else {
        header("Location: signup.html?error_name=" . urlencode("Database error: " . $stmt->error));
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
