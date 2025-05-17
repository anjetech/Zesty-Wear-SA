<?php
// Start session
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zestywearsa";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prevent SQL injection
    $email = $conn->real_escape_string($email);

    // Prepare SQL statement to check user existence
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $stored_password);
        $stmt->fetch();

        // Compare password directly (since it's not hashed)
        if ($password === $stored_password) {
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;

            // Redirect to main page
            header("Location: main.html");
            exit();
        } else {
            echo "<script>
                alert('Invalid email or password. Please try again.');
                window.location.href = 'login.html';
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('No account found with this email.');
            window.location.href = 'login.html';
        </script>";
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
