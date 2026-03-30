<!-- create_user.php - A form to create a new user in the database.
Validates input, hashes password, and displays a success message. Admin users only. -->

<?php
require('authenticate.php'); // 7.1 page for admin users only
require('db_connect.php'); // connect to db using PDO

$message = '';

// Check if form was submitted
if ($_POST
    && !empty($_POST['username'])
    && !empty($_POST['password'])
    && !empty($_POST['role'])):

    // Sanitize inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = $_POST['password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // 7.3 Hash password (salting + hashing)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 7.2 INSERT new user into users table
    $query = "INSERT INTO users (username, password, role)
              VALUES (:username, :password, :role)";
    $statement = $db->prepare($query);

    // Bind values (PDO requirement)
    $statement->bindValue(':username', $username);
    $statement->bindValue(':password', $hashed_password);
    $statement->bindValue(':role', $role);

    // execute query
    if ($statement->execute()):
        $message = "User created successfully.";
    endif;

endif;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <h1>Create User</h1>

        <?php if ($message): ?>
            <p><?= $message ?></p>
        <?php endif; ?>

        <form method="post" action="create_user.php">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <input type="submit" value="Create User">
        </form>

        <p class="content-link-wrap">
            <a class="content-link" href="manage_users.php">Back to Manage Users</a>
        </p>
    </main>
</body>
</html>