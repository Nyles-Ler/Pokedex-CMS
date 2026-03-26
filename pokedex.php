/* pokedex.php - A page to display all Pokémon entries in the database. */

<?php
session_start();
require('db_connect.php');

/* query all Pokémon from database */
$query = "SELECT * FROM pokemon ORDER BY pokedex_number ASC";
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
    <title>Pokédex - Kowski Komics</title>    
</head>
<body class="page-pokedex">
    <main>
        <header>
            <img class="Kowskilogo" src="images/kowskilogo.jpg" alt="Kowski Komics Logo">

            <nav aria-label="User Navigation">
                <ul>
                    /* show login/logout links based on session */
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php">Log Out</a></li>

                        /* if admin, show link to admin dashboard */
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
            <h1>Kowski Komics Local Pokédex</h1>
            <h2><i>Browse all Pokémon in the database</i></h2>
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
                Welcome to the Kowski Komics Pokédex. Browse Pokémon entries, view their types and stats,
                and discover information about each Pokémon in the database.
            </p>
        </aside>

        <section>
            <article id="content">
                <h3>Pokédex Entries</h3>
                
                /* if Pokémon found, loop through and display each entry */
                <?php if ($statement->rowCount() > 0): ?>
                    <?php while ($pokemon = $statement->fetch()): ?>
                        <p class="p1">
                            <strong>#<?= $pokemon['pokedex_number'] ?> - <?= htmlspecialchars($pokemon['name']) ?></strong>
                        </p>

                        <p class="middle-paragraph">
                            <strong>Type:</strong>
                            <?= htmlspecialchars($pokemon['type1']) ?>
                            <?= $pokemon['type2'] ? ' / ' . htmlspecialchars($pokemon['type2']) : '' ?>
                        </p>

                        <div class="imagecontent">
                            <?php if (!empty($pokemon['image'])): ?>
                                <img class="pokemon" src="<?= htmlspecialchars($pokemon['image']) ?>" alt="<?= htmlspecialchars($pokemon['name']) ?>">
                            <?php else: ?>
                                <img class="pokemon" src="images/pikachu.png" alt="Pokémon placeholder">
                            <?php endif; ?>

                            <div>
                                <p><strong>HP:</strong> <?= $pokemon['hp'] ?></p>
                                <p><strong>Attack:</strong> <?= $pokemon['attack_stat'] ?></p>
                                <p><strong>Defense:</strong> <?= $pokemon['defense_stat'] ?></p>
                                <p><strong>Special Attack:</strong> <?= $pokemon['special_attack'] ?></p>
                                <p><strong>Special Defense:</strong> <?= $pokemon['special_defense'] ?></p>
                                <p><strong>Speed:</strong> <?= $pokemon['speed_stat'] ?></p>

                                <?php if (!empty($pokemon['height'])): ?>
                                    <p><strong>Height:</strong> <?= $pokemon['height'] ?> m</p>
                                <?php endif; ?>

                                <?php if (!empty($pokemon['weight'])): ?>
                                    <p><strong>Weight:</strong> <?= $pokemon['weight'] ?> kg</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p class="middle-paragraph">
                            <strong>Description:</strong> <?= htmlspecialchars($pokemon['description']) ?>
                        </p>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                            <p class="middle-paragraph">
                                <a href="edit_pokemon.php?id=<?= $pokemon['id'] ?>">Edit</a>
                            </p>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="middle-paragraph">No Pokémon have been added yet.</p>
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