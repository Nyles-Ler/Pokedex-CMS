<!-- register.php - A page for users to register their own account. -->

<?php
require('db_connect.php'); // connect to db using PDO

$message = '';
$username = '';
$email = '';

/* check if form was submitted */
if ($_POST
    && !empty($_POST['username'])
    && !empty($_POST['email'])
    && !empty($_POST['password'])
    && !empty($_POST['confirm_password'])):

    /* sanitize inputs */
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    /* check if passwords match */
    if ($password !== $confirm_password):
        $message = "Passwords do not match. Please try again.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)):
        $message = "Please enter a valid email address.";
    else:
        /* hash password */
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        /* role is always regular user for self-registration */
        $role = 'user';

        /* insert new registered user */
        $query = "INSERT INTO users (username, email, password, role)
                  VALUES (:username, :email, :password, :role)";
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $hashed_password);
        $statement->bindValue(':role', $role);

        if ($statement->execute()):
            $message = "Account created successfully. You can now log in.";
        else:
            $message = "There was a problem creating your account.";
        endif;
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
    <title>Register - Kowski Komics</title>
</head>
<body class="page-admin-form">
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
            <h1>Register</h1>
            <h2><i>Create your own user account</i></h2>
        </div>

        <section>
            <article id="content">
                <h3>Register</h3>

                <?php if ($message): ?>
                    <p class="middle-paragraph"><?= htmlspecialchars($message) ?></p>
                <?php endif; ?>

                <form method="post" action="register_user.php">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="<?= htmlspecialchars($username) ?>" required>

                    <label for="email">Email Address</label>
                    <input id="email" name="email" type="email" value="<?= htmlspecialchars($email) ?>" required>

                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required>

                    <label for="confirm_password">Confirm Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" required>

                    <input type="submit" value="Register">
                </form>

                <p class="content-link-wrap">
                    <a class="content-link" href="login.php">Back to Log In</a>
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
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>
            <p>Copyright &copy; 2026 Kowski Komics. All rights reserved.</p>
            <p class="p2">160 Princess Street, Winnipeg, MB R3B 1K9</p>
        </footer>
    </main>
</body>
</html>