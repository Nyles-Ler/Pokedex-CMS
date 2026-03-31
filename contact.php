<!-- contact.php - A simple contact form that validates input and displays a success message. -->

<?php
session_start();

$success = false;

if ($_POST
    && !empty($_POST['name'])
    && !empty($_POST['email'])
    && !empty($_POST['message'])):

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($email):
        // Doesnt do anything
        $success = true;
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
    <title>Contact Us - Kowski Komics</title>
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
                            <li><a href="index.php">Home</a></li>
                        <?php endif; ?>

                    <?php else: ?>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="login.php">Log In</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <div id="mainheading">
            <h1>Contact Kowski Komics</h1>
            <h2><i>Have a question? Reach out to us!</i></h2>
        </div>

        <section>
            <article id="content">
                <h3>Contact Form</h3>

                <?php if ($success): ?>
                    <p class="middle-paragraph">
                        Thank you, <?= $name ?>! Your message has been received.
                    </p>
                <?php else: ?>

                    <form method="post" action="contact.php">

                        <label for="name">Name</label>
                        <input id="name" name="name" type="text" required>

                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" required>

                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>

                        <input type="submit" value="Send Message">

                    </form>

                <?php endif; ?>

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