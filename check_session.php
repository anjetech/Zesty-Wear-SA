<?php
//Used this check session in my login
session_start();
$alert = isset($_SESSION['login_alert']);
unset($_SESSION['login_alert']); // Remove flag after checking
echo json_encode(["showAlert" => $alert]);
?>