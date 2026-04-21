<!-- manage_users.php - A page to display all users in the database 
with options to edit or delete them. Admin users only. -->

<?php
require('authenticate.php'); // 7.1 admin only access
require('db_connect.php');

/* read all users from database */
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
                <h1>Manage Users</h1>
                <h2><i>View and manage all user accounts</i></h2>
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

                    <p class="p1"><strong>Found <?= $statement->rowCount() ?> Users</strong></p>

                    <ul class="user-list">
                        <?php while ($user = $statement->fetch()): ?>
                            <li>
                                <p class="middle-paragraph">
                                    <strong>ID:</strong> <?= $user['id'] ?> |
                                    <strong>Username:</strong> <?= htmlspecialchars($user['username']) ?> |
                                    <strong>Role:</strong> <?= htmlspecialchars($user['role']) ?>
                                </p>

                                <p class="content-link-wrap">
                                    <a class="content-link" href="edit_user.php?id=<?= $user['id'] ?>">Edit User</a>
                                </p>
                            </li>
                        <?php endwhile; ?>
                    </ul>

                    <p class="content-link-wrap">
                        <a class="content-link" href="create_user.php">Create User</a>
                    </p>

                    <p class="content-link-wrap">
                        <a class="content-link" href="delete_user.php">Delete User</a>
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
                    </ul>
                </nav>
            </footer>

        </main>
    </body>
</html>