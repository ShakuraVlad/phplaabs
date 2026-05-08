<?php
session_start();
require "config.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE email = ?"
    );

    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {

        $_SESSION["user"] = $user["username"];

        header("Location: index.php");
        exit;

    } else {
        $errors[] = "Неверный логин или пароль";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-form">

    <h2>Вход</h2>

    <?php foreach($errors as $error): ?>
        <p style="color:red;">
            <?= $error ?>
        </p>
    <?php endforeach; ?>

    <form method="POST">

        <input type="email" name="email" placeholder="Email">

        <input type="password" name="password" placeholder="Пароль">

        <button type="submit">
            Войти
        </button>

    </form>

</div>

</body>
</html>