<!-- pokedex.php - A page to display all Pokémon entries in the database. -->

<?php
session_start();
require('db_connect.php');

/* query all Pokémon from database */
$query = "
    SELECT pokemon.*, types.color AS type_color
    FROM pokemon
    LEFT JOIN types ON pokemon.type1 = types.name
    ORDER BY pokedex_number ASC
";
$statement = $db->prepare($query);
$statement->execute();

/* query one random Pokémon for the pokemon for featured */
$featured_query = "SELECT * FROM pokemon ORDER BY RAND() LIMIT 1";
$featured_statement = $db->prepare($featured_query);
$featured_statement->execute();
$featured_pokemon = $featured_statement->fetch();
$query = "
    SELECT pokemon.*, types.color AS type_color
    FROM pokemon
    LEFT JOIN types ON pokemon.type1 = types.name
    ORDER BY pokedex_number ASC
";
$statement = $db->prepare($query);
$statement->execute();

/* query db for admin dashboard colour types gradient */
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
                    <!-- show login/logout links based on session -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php">Log Out</a></li>

                        <!-- if admin, show link to admin dashboard -->
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
            <h1>Kowski Komics Local Pokédex</h1>
            <h2><i>Browse all Pokémon in the database</i></h2>
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

        <!-- sidebar with featured Pokémon -->
        <aside>
            <?php if ($featured_pokemon): ?>
                <p>
                    <!-- if Pokémon has image, show it, otherwise show placeholder image -->
                    <?php if (!empty($featured_pokemon['image'])): ?>
                        <img class="pokemon" src="<?= htmlspecialchars($featured_pokemon['image']) ?>" alt="<?= htmlspecialchars($featured_pokemon['name']) ?>">
                    <?php else: ?>
                        <img class="pokemon" src="images/pikachu.png" alt="Pokemon placeholder">
                    <?php endif; ?>
                    
                    <strong>Featured Pokemon:</strong><br>
                    <!-- show Pokémon name and description, with special characters escaped 
                     for security -->
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
                <h3>Pokédex Entries</h3>
                
                <!-- if Pokémon found, loop through and display each entry -->
                <?php if ($statement->rowCount() > 0): ?>
                    <!-- loop through Pokémon and display details, with special 
                     characters escaped for security -->
                    <?php while ($pokemon = $statement->fetch()): ?>
                        <p class="p1">
                            <strong>#<?= $pokemon['pokedex_number'] ?> - <?= htmlspecialchars($pokemon['name']) ?></strong>
                        </p>

                        <p class="middle-paragraph">
                            <strong>Type:</strong>
                            <?= htmlspecialchars($pokemon['type1']) ?>
                            <?= $pokemon['type2'] ? ' / ' . htmlspecialchars($pokemon['type2']) : '' ?>
                        </p>
                        <!-- show Pokémon image and stats, with special characters escaped 
                         for security -->
                        <div class="imagecontent" style="background: linear-gradient(135deg, <?= htmlspecialchars($pokemon['type_color']) ?>, #ffffff);">
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
                        
                        <!-- if user is admin, show edit link for each Pokémon -->
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