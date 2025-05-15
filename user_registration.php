<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zestywearsa";

// Create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
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
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email is already registered! Please use a different one.'); window.history.back();</script>";
        exit();
    }
    $check_email->close();

    // Store plain text password (INSECURE - for testing only)
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful! Redirecting to the main page...');
                window.location.href='main.html';
              </script>";
    } else {
        echo "<script>alert('Database error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
