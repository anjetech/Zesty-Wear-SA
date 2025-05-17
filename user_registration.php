<?php
session_start();

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zestywearsa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<script>alert('Database connection failed: " . $conn->connect_error . "');</script>");
}

$conn->set_charset("utf8");

// Handle registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']); 
    $email = trim($_POST['email']); 
    $password = trim($_POST['password']); 
    $confirm_password = trim($_POST['confirm_password']); 

    if (empty($password)) {
        echo "<script>alert('Error: Password field is empty!'); window.history.back();</script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Error: Passwords do not match! Please try again.'); window.history.back();</script>";
        exit();
    }

    // Check if email is already registered
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email is already registered! Please use a different one.'); window.history.back();</script>";
        exit();
    }
    $check_email->close();

    // Insert user with plain-text password (consider hashing for security!)
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id; // Automatically log in the user

        echo "<script>
                alert('Registration successful! Redirecting to your profile...');
                window.location.href='profile.php';
              </script>";
    } else {
        echo "<script>alert('Database error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
