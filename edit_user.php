<!-- edit_user.php - A page to edit an existing user in the database. Admin users only. -->

<?php
require('authenticate.php'); // 7.1 admin only access
require('db_connect.php');

/* if form was submitted, update user info */
if ($_POST
    && isset($_POST['id'])
    && !empty($_POST['username'])
    && !empty($_POST['role'])):

    /* sanitize input */
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = $_POST['password'];

    /* if password was entered, update password */
    if (!empty($password)):

        /* 7.3 hash password before updating */
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        /* 7.2 update user with the new password */
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

        /* 7.2 update user without updating password */
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Edit User - Kowski Komics</title>
</head>
    <body class="page-home">
        <main>

            <header>
                <img class="Kowskilogo" src="images/kowskilogo.jpg" alt="Kowski Komics Logo">

                <nav aria-label="User Navigation">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <!-- show login/logout links based on session -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="logout.php">Log Out</a></li>
                            <?php else: ?>
                                <li><a href="login.php">Log In</a></li>
                                <li><a href="register_user.php">Register</a></li>
                                <li><a href="forgot_password.php">Forgot Password</a></li>
                        <?php endif; ?>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </nav>

                <div class="search-group">
                    <label for="search">Search</label>
                    <input id="search" type="text">
                    <button type="submit">Search</button>
                </div>
            </header>

            <div id="mainheading">
                <h1>Edit User</h1>
                <h2><i>Update an existing user account</i></h2>
            </div>

            <div id="navrow">
            <!-- if user is logged in and is admin, show admin dashboard section -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
            <div class="admin-dashboard-row">
                <article id="admin-dashboard" class="admin-card">
                    <h3>Admin Dashboard</h3>
                    <p>Welcome back, <?= $_SESSION['username']; ?>!</p>
                    <nav aria-label="Admin-Navigation">
                        <ul class="admin-links">
                            <li><a href="create_pokemon.php">Create Pokemon</a></li>
                            <li><a href="edit_pokemon.php">Update Pokemon</a></li>
                            <li><a href="delete_pokemon.php">Delete Pokemon</a></li>
                            <li><a href="manage_users.php">Manage User</a></li>
                        </ul>
                    </nav>
                </article>
            </div>
            <?php endif; ?>
                <nav aria-label="Main Navigation">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="pokedex.php">Browse Pokédex</a></li>
                    </ul>
                </nav>
            </div>

            <section>
                <article id="content">

                    <p class="p1"><strong>Select a User</strong></p>

                    <ul class="user-list">
                        <?php while ($user_row = $list_statement->fetch()): ?>
                            <li>
                                <p class="content-link-wrap">
                                    <a class="content-link" href="edit_user.php?id=<?= $user_row['id'] ?>">
                                        <?= htmlspecialchars($user_row['username']) ?> (<?= htmlspecialchars($user_row['role']) ?>)
                                    </a>
                                </p>
                            </li>
                        <?php endwhile; ?>
                    </ul>

                    <?php if ($id && $user): ?>
                        <p class="p1"><strong>Editing: <?= htmlspecialchars($user['username']) ?></strong></p>

                        <form method="post" action="edit_user.php">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">

                            <label for="username">Username</label>
                            <input id="username" name="username" type="text" value="<?= htmlspecialchars($user['username']) ?>" required>

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
                        <p class="middle-paragraph">Select a user from the list above.</p>
                    <?php endif; ?>

                    <p class="content-link-wrap">
                        <a class="content-link" href="manage_users.php">Back to Manage Users</a>
                    </p>

                    <p class="content-link-wrap">
                        <a class="content-link" href="index.php">Back to Home</a>
                    </p>

                </article>
            </section>

            <footer>
                <nav aria-label="Footer Navigation">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="pokedex.php">Pokedex</a></li>
                    </ul>
                </nav>
            </footer>

        </main>
    </body>
</html>