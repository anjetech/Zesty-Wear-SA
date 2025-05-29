<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "zestywearsa";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

$sql = "SELECT * FROM product WHERE category='Men Accessories'";
$result = $conn->query($sql);

$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            "name" => $row["name"],
            "price" => "R" . $row["price"],
            "description" => $row["description"],
            "image" => $row["image_path"], // No 'uploads/' prefix
            "id" => $row["id"]
        ];
    }
}

echo json_encode($products);
?>
