<?php
require "config.php";

$username = "admin";
$email = "admin@mail.com";
$password = "123456";

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->execute([$username, $email, $hash]);

echo "Пользователь создан";