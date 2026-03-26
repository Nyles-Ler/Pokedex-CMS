<?php
session_start();
require('db_connect.php');

$message = "";

if ($_POST && !empty($_POST['username'])):

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->execute();

    $user = $statement->fetch();

    if ($user):
        // Doesnt connect to anything
        $message = "User found. Please contact admin to reset your password.";
    else:
        $message = "No account found with that username.";
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
    <title>Forgot Password - Kowski Komics</title>
</head>
<body class="page-admin-form">
    <main>
        <header>
            <img class="Kowskilogo" src="images/kowskilogo.jpg" alt="Kowski Komics Logo">

            <nav aria-label="User Navigation">
                <ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php">Log Out</a></li>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li><a href="index.php#admin-dashboard">Admin Dashboard</a></li>
                        <?php endif; ?>

                    <?php else: ?>
                        <li><a href="login.php">Log In</a></li>
                    <?php endif; ?>

                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>
        </header>

        <div id="mainheading">
            <h1>Forgot Password</h1>
            <h2><i>Recover access to your account</i></h2>
        </div>

        <section>
            <article id="content">
                <h3>Reset Password</h3>

                <?php if ($message): ?>
                    <p class="middle-paragraph"><?= $message ?></p>
                <?php endif; ?>

                <form method="post" action="forgot_password.php">

                    <label for="username">Enter your username</label>
                    <input id="username" name="username" type="text" required>

                    <input type="submit" value="Check Account">

                </form>

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
                    <li><a href="pokemontypes.php">Pokemon Types</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>
            <p>Copyright &copy; 2026 Kowski Komics. All rights reserved.</p>
            <p class="p2">160 Princess Street, Winnipeg, MB R3B 1K9</p>
        </footer>
    </main>
</body>
</html>