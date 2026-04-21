<!-- create_user.php - A form to create a new user in the database.
Validates input, hashes password, and displays a success message. Admin users only. -->

<?php
require('authenticate.php'); // 7.1 page for admin users only
require('db_connect.php'); // connect to db using PDO

$message = '';

/* check if form is submitted using POST */
if ($_POST
    && !empty($_POST['username'])
    && !empty($_POST['password'])
    && !empty($_POST['role'])):

    /* sanitize inputs */
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = $_POST['password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    /* 7.3 hash password */
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    /* 7.2 insert new user */
    $query = "INSERT INTO users (username, password, role)
              VALUES (:username, :password, :role)";
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':password', $hashed_password);
    $statement->bindValue(':role', $role);

    if ($statement->execute()):
        $message = "User created successfully.";
    endif;

endif;
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
    <title>Create User - Kowski Komics</title>
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
                <h1>Create User</h1>
                <h2><i>Add a new user account</i></h2>
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

                    <!-- success message -->
                    <?php if ($message): ?>
                        <p class="middle-paragraph"><?= htmlspecialchars($message) ?></p>
                    <?php endif; ?>

                    <p class="p1"><strong>Create a New User</strong></p>

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

                    <!-- navigation -->
                    <p class="content-link-wrap">
                        <a class="content-link" href="manage_users.php">Back to Manage Users</a>
                    </p>

                    <p class="content-link-wrap">
                        <a class="content-link" href="index.php#admin-dashboard">Back to Dashboard</a>
                    </p>

                </article>
            </section>

            <!-- FOOTER -->
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