/* delete_user.php - A page to delete a user from the database. Admin users only. */

<?php
require('authenticate.php'); // 7.1 admin only access
require('db_connect.php');

$message = '';

// if delete form is submitted
if ($_POST && isset($_POST['id'])):

    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    // 7.2 DELETE user from db
    $query = "DELETE FROM users WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    $message = "User deleted successfully.";

endif;

// 7.2 SELECT users to display list
$query = "SELECT id, username, role FROM users ORDER BY username ASC";
$statement = $db->prepare($query);
$statement->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <h1>Delete User</h1>

        <?php if ($message): ?>
            <p><?= $message ?></p>
        <?php endif; ?>

        <ul class="user-list">
            <?php while ($user = $statement->fetch()): ?>
                <li>
                    <?= $user['username'] ?> (<?= $user['role'] ?>)

                    <form method="post" action="delete_user.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <input type="submit" value="Delete">
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>

        <p class="content-link-wrap">
            <a class="content-link" href="manage_users.php">Back to Manage Users</a>
        </p>
    </main>
</body>
</html>