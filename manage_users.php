<!-- manage_users.php - A page to display all users in the database 
with options to edit or delete them. Admin users only. -->

<?php
require('authenticate.php'); // 7.1 admin only access
require('db_connect.php');

// READ all users from db
$query = "SELECT id, username, role FROM users ORDER BY username ASC";
$statement = $db->prepare($query);
$statement->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Manage Users - Kowski Komics</title>
</head>
<body>
    <main>
        <h1>Manage Users</h1>

        <!-- display number of users found -->
        <h2>Found <?= $statement->rowCount() ?> Users</h2>

        <ul class="user-list">
            <!-- loop through users and display with edit link -->
            <?php while ($user = $statement->fetch()): ?>
                <li>
                    <!-- show user info and link to edit page -->
                    ID: <?= $user['id'] ?> |
                    Username: <?= $user['username'] ?> |
                    Role: <?= $user['role'] ?>

                    <a class="content-link" href="edit_user.php?id=<?= $user['id'] ?>">Edit</a>
                </li>
            <?php endwhile; ?>
        </ul>

        <p class="content-link-wrap">
            <a class="content-link" href="create_user.php">Create User</a>
            <a class="content-link" href="delete_user.php">Delete User</a>
            <a class="content-link" href="index.php#admin-dashboard">Back to Dashboard</a>
        </p>
    </main>
</body>
</html>