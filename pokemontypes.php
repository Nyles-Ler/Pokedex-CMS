<!-- pokemontypes.php - A page to display all Pokémon types in the database. -->

<?php
session_start();
require('db_connect.php');

$query = "SELECT * FROM types ORDER BY name ASC";
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
    <title>Pokémon Types - Kowski Komics</title>
</head>
<body class="page-types">
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

                    <li><a href="forgot_password.php">Forgot Password</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>

            <label for="search">Search</label>
            <input id="search" type="text">
            <button type="submit">Search</button>
        </header>

        <div id="mainheading">
            <h1>Kowski Komics Pokémon Types</h1>
            <h2><i>Explore the different Pokémon types in the database</i></h2>
        </div>

        <div id="navrow">
            <nav aria-label="Main Navigation">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="pokedex.php">Browse Pokédex</a></li>
                    <li><a href="pokemontypes.php">Explore Pokémon Types</a></li>
                </ul>
            </nav>
        </div>

        <aside>
            <p>
                <img class="pokemon" src="images/pikachu.png" alt="Pikachu">
                Pokémon types affect strengths, weaknesses, and battle strategy. Browse the full list of types used in the Kowski Komics Pokédex database.
            </p>
        </aside>

        <section>
            <article id="content">
                <h3>Pokémon Type List</h3>

                <?php if ($statement->rowCount() > 0): ?>
                    <?php while ($type = $statement->fetch()): ?>
                        <p class="p1">
                            <strong><?= htmlspecialchars($type['name']) ?></strong>
                        </p>

                        <div class="imagecontent">
                            <img class="pokemon" src="images/pikachu.png" alt="Pokémon type icon placeholder">

                            <div>
                                <p><strong>Type Name:</strong> <?= htmlspecialchars($type['name']) ?></p>

                                <?php if (!empty($type['color'])): ?>
                                    <p><strong>Color Code:</strong> <?= htmlspecialchars($type['color']) ?></p>
                                    <p>
                                        <strong>Preview:</strong>
                                        <span class="type-color-preview" style="background-color: <?= htmlspecialchars($type['color']) ?>;"></span>
                                    </p>
                                <?php else: ?>
                                    <p><strong>Color Code:</strong> Not available</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p class="middle-paragraph">
                            The <strong><?= htmlspecialchars($type['name']) ?></strong> type is one of the battle categories used to classify Pokémon and their moves.
                        </p>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="middle-paragraph">No Pokémon types have been added yet.</p>
                <?php endif; ?>
            </article>
        </section>

        <footer>
            <nav aria-label="Footer Navigation">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="pokedex.php">Pokedex</a></li>
                    <li><a href="pokemontypes.php">Pokemon Types</a></li>
                    <li><a href="#">Our Staff</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </nav>
            <p>Copyright &copy; 2026 Kowski Komics. All rights reserved.</p>
            <p class="p2">160 Princess Street, Winnipeg, MB R3B 1K9</p>
        </footer>
    </main>
</body>
</html>