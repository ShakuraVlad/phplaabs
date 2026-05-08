<?php
session_start();
require "config.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Валидация
    if (empty($username)) {
        $errors[] = "Введите логин";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Неверный email";
    }

    if (strlen($password) < 6) {
        $errors[] = "Пароль минимум 6 символов";
    }

    // Проверка существования пользователя
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);

    if ($stmt->fetch()) {
        $errors[] = "Пользователь уже существует";
    }

    if (empty($errors)) {

        // Хеширование пароля
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            "INSERT INTO users(username, email, password)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([
            htmlspecialchars($username),
            htmlspecialchars($email),
            $hashedPassword
        ]);

        $_SESSION["user"] = $username;

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-form">

    <h2>Регистрация</h2>

    <?php foreach($errors as $error): ?>
        <p style="color:red;">
            <?= $error ?>
        </p>
    <?php endforeach; ?>

    <form method="POST">

        <input type="text" name="username" placeholder="Логин">

        <input type="email" name="email" placeholder="Email">

        <input type="password" name="password" placeholder="Пароль">

        <button type="submit">
            Зарегистрироваться
        </button>

    </form>

</div>

</body>
</html>