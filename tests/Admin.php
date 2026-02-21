<?php
// require_once "Connection.php";

// $pdo = Connection::make();

$name = "Admin";
$email = "admin@healthcare.com";
$password = password_hash("Admin@12345", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $email, $password, $role]);

echo "Admin Created Successfully!";
?>