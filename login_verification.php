<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	$sql = "SELECT * FROM users WHERE email='$email'";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		if (password_verify($password, $row['password'])) {
			$_SESSION['user_id'] = $row['id'];
			header("Location: profile.php");
		}else{
			echo "Invalid password.";
		}else {
			echo "No user found with this email.";
		}
	}
?>
	
