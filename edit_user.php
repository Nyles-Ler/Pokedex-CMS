<!-- edit_user.php - A page to edit an existing user in the database. Admin users only. -->

<?php
require('authenticate.php'); // 7.1 admin only access
require('db_connect.php');

// if form was submitted, update user info
if ($_POST
    && isset($_POST['id'])
    && !empty($_POST['username'])
    && !empty($_POST['role'])):

    /* sanitize input */
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = $_POST['password'];

    // if password was entered, update password
    if (!empty($password)):

        // 7.3 hash password before updating
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 7.2 UPDATE user with the new password
        $query = "UPDATE users
                  SET username = :username,
                      password = :password,
                      role = :role
                  WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $hashed_password);
        $statement->bindValue(':role', $role);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    else:

        // 7.2 UPDATE user without updating password
        $query = "UPDATE users
                  SET username = :username,
                      role = :role
                  WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':role', $role);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    endif;

    /* redirect back to edit page to show updated info */
    header("Location: edit_user.php?id={$id}");
    exit;

elseif (isset($_GET['id'])):

    /* sanitize input */
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT id, username, role FROM users WHERE id = :id LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    $user = $statement->fetch();

else:
    $id = false;
endif;

/* query users to display list of users with edit links */
$list_query = "SELECT id, username, role FROM users ORDER BY username ASC";
$list_statement = $db->prepare($list_query);
$list_statement->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <h1>Edit User</h1>

        <ul class="user-list">
            <!-- loop through users and display with edit link -->
            <?php while ($user_row = $list_statement->fetch()): ?>
                <li>
                    <!-- show username and role with link to edit page -->
                    <a href="edit_user.php?id=<?= $user_row['id'] ?>">
                        <?= $user_row['username'] ?> (<?= $user_row['role'] ?>)
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>

        <!-- if user selected, show edit form pre-filled with user data -->
        <?php if ($id && $user): ?>
            <form method="post" action="edit_user.php">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">

                <label for="username">Username</label>
                <input id="username" name="username" type="text" value="<?= $user['username'] ?>" required>

                <label for="password">New Password (leave blank to keep current password)</label>
                <input id="password" name="password" type="password">

                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>

                <input type="submit" value="Update User">
            </form>
        <?php else: ?>
            <p>Select a user from the list above.</p>
        <?php endif; ?>

        <p class="content-link-wrap">
            <a class="content-link" href="manage_users.php">Back to Manage Users</a>
        </p>
    </main>
</body>
</html>