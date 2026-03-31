<!-- pokemontypes.php - A page to display all Pokémon types in the database. -->

<?php
session_start();
require('db_connect.php');

/* query for types */
$query = "SELECT * FROM types ORDER BY name ASC";
$statement = $db->prepare($query);
$statement->execute();

/* query for featured pokemon */
$featured_query = "SELECT * FROM pokemon ORDER BY RAND() LIMIT 1";
$featured_statement = $db->prepare($featured_query);
$featured_statement->execute();
$featured_pokemon = $featured_statement->fetch();

/* query to get all unique colors from types table for admin dashboard background */
$color_query = "SELECT color FROM types WHERE color IS NOT NULL";
$color_statement = $db->prepare($color_query);
$color_statement->execute();

$colors = [];

while ($row = $color_statement->fetch()):
    $colors[] = $row['color'];
endwhile;

$gradient = '';

if (!empty($colors)):
    $gradient = 'linear-gradient(135deg, ' . implode(', ', $colors) . ')';
else:
    $gradient = 'linear-gradient(135deg, #d9f99d, #a0e7e5)';
endif;

/* static function to render type badges with colors */
function render_type_badges($types_string) {
    if (!$types_string) return '';
    /* split comma-separated types into array and trim whitespace */
    $types = explode(',', $types_string);
    /* hard coded colors for each type */
    $type_colors = [
        'Fire' => '#F08030',
        'Water' => '#6890F0',
        'Grass' => '#78C850',
        'Electric' => '#F8D030',
        'Ice' => '#98D8D8',
        'Fighting' => '#C03028',
        'Poison' => '#A040A0',
        'Ground' => '#E0C068',
        'Rock' => '#B8A038',
        'Bug' => '#A8B820',
        'Ghost' => '#705898',
        'Dragon' => '#7038F8',
        'Dark' => '#705848',
        'Steel' => '#B8B8D0',
        'Psychic' => '#F85888',
        'Fairy' => '#EE99AC',
        'Flying' => '#A890F0',
        'Normal' => '#A8A878'
    ];

    $output = '';
    /* loop through types and create badge for each,
     using color from array or default if not found */
    foreach ($types as $type):
        $type = trim($type);
        $color = $type_colors[$type] ?? '#777';
        /* create a span with class for styling and inline background color */
        $output .= "<span class='type-badge' style='background:$color;'>$type</span>";
    endforeach;

    return $output;
}

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

            <div class="search-group">
                <label for="search">Search</label>
                <input id="search" type="text">
                <button type="submit">Search</button>
            </div>
        </header>  

        <div id="mainheading">
            <h1>Kowski Komics Pokémon Types</h1>
            <h2><i>Explore the different Pokémon types in the database</i></h2>
        </div>

        <div id="navrow">
        <!-- if user is logged in and is admin, show admin dashboard section -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
        <div class="admin-dashboard-row">
            <article id="admin-dashboard" class="admin-card" style="background: <?= htmlspecialchars($gradient) ?>;">
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
                    <li><a href="pokemontypes.php">Explore Pokémon Types</a></li>
                </ul>
            </nav>
        </div>

        <aside>
            <?php if ($featured_pokemon): ?>
                <p>
                    <?php if (!empty($featured_pokemon['image'])): ?>
                        <img class="pokemon"
                             src="<?= htmlspecialchars($featured_pokemon['image']) ?>"
                             alt="<?= htmlspecialchars($featured_pokemon['name']) ?>">
                    <?php else: ?>
                        <img class="pokemon" src="images/pikachu.png" alt="Pokemon placeholder">
                    <?php endif; ?>

                    <strong>Featured Pokemon:</strong><br>
                    <?= htmlspecialchars($featured_pokemon['name']) ?><br><br>
                    <?= htmlspecialchars($featured_pokemon['description']) ?>
                </p>
            <?php else: ?>
                <p>
                    <img class="pokemon" src="images/pikachu.png" alt="Pikachu">
                    No Pokemon available yet.
                </p>
            <?php endif; ?>
        </aside>

        <section>
            <article id="content">
                <h3>Pokémon Type List</h3>
                <p>A Pokémon may have either one or two types. For instance, Charmander is a Fire type, while Bulbasaur is both a Grass type and a Poison type. Pokémon with two types are known as dual-type Pokémon, while Pokémon with only one type are known as pure type Pokémon. With this system and there currently being 18 types, there is a total of 324 possible ways to assign types to Pokémon, with 171 unique combinations, 162 of which have been used as of Generation IX.</p>

                <?php if ($statement->rowCount() > 0): ?>
                    <?php while ($type = $statement->fetch()): ?>
                        <p class="p1">
                            <strong><?= htmlspecialchars($type['name']) ?></strong>
                        </p>

                        <div class="imagecontent">
                            <img class="pokemon" src="images/pikachu.png" alt="Pokémon type icon placeholder">

                            <div>
                                <p><strong>Type Name:</strong> <?= htmlspecialchars($type['name']) ?></p>
                                <?php if (!empty($type['description'])): ?>
                                <p><strong>Description:</strong> <?= htmlspecialchars($type['description']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($type['strengths'])): ?>
                                <p><strong>Strong Against:</strong></p>
                                <div class="type-badge-container">
                                    <?= render_type_badges($type['strengths']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($type['weaknesses'])): ?>
                                <p><strong>Weak Against:</strong></p>
                                <div class="type-badge-container">
                                    <?= render_type_badges($type['weaknesses']) ?>
                                </div>
                            <?php endif; ?>

                                <?php if (!empty($type['color'])): ?>
                                    <p><strong>Color Code:</strong> <?= htmlspecialchars($type['color']) ?></p>
                                    <p>
                                        <strong>Colour:</strong>
                                        <span class="type-color-preview" style="background-color: <?= htmlspecialchars($type['color']) ?>;"></span>
                                    </p>
                                <?php else: ?>
                                    <p><strong>Colour Code:</strong> Not available</p>
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