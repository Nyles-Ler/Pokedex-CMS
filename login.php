<?php
session_start();
require('db_connect.php');

$error = '';

if($_POST && !empty($_POST['username']) && !empty($_POST['password'])) {
    
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->execute();

    $user = $statement->fetch();

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
    
        <?php if ($error): ?>
            <p><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>