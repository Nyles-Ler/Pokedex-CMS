<!-- delete_user.php - A page to delete a user from the database. Admin users only. -->

<?php
require('authenticate.php'); // 7.1 admin only access
require('db_connect.php');

$message = '';

/* if delete form is submitted */
if ($_POST && isset($_POST['id'])):

    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    /* 7.2 delete user from database */
    $query = "DELETE FROM users WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    $message = "User deleted successfully.";

endif;

/* 7.2 select users to display list */
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
    <title>Delete User - Kowski Komics</title>
</head>
    <body class="page-home">
        <main>

            <!-- HEADER -->
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

            <!-- MAIN HEADING -->
            <div id="mainheading">
                <h1>Delete User</h1>
                <h2><i>Remove user accounts from the system</i></h2>
            </div>

            <!-- NAV -->
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

            <!-- CONTENT -->
            <section>
                <article id="content">

                    <!-- message -->
                    <?php if ($message): ?>
                        <p class="middle-paragraph"><?= htmlspecialchars($message) ?></p>
                    <?php endif; ?>

                    <p class="p1"><strong>Select a User to Delete</strong></p>

                    <ul class="user-list">

                        <?php while ($user = $statement->fetch()): ?>

                            <li>

                                <p class="content-link-wrap">
                                    <span class="content-link">
                                        <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['role']) ?>)
                                    </span>
                                </p>

                                <form method="post" action="delete_user.php">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">

                                    <input type="submit"
                                           value="Delete User"
                                           onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($user['username']) ?>?');">
                                </form>

                            </li>

                        <?php endwhile; ?>

                    </ul>

                    <p class="content-link-wrap">
                        <a class="content-link" href="manage_users.php">Back to Manage Users</a>
                    </p>

                    <p class="content-link-wrap">
                        <a class="content-link" href="index.php#admin-dashboard">Back to Dashboard</a>
                    </p>

                </article>
            </section>

            <footer>
                <nav aria-label="Footer Navigation">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="pokedex.php">Pokedex</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </nav>
                <p>Copyright &copy; 2026 Kowski Komics. All rights reserved.</p>
                <p class="p2">160 Princess Street, Winnipeg, MB R3B 1K9</p>
            </footer>

        </main>
    </body>
</html>