<!-- pokedex.php - A page to display all Pokémon entries in the database. -->
<?php
session_start();
require('db_connect.php');


/* 2.3 view list of pages/products with database-level sorting */
/* 6.1 display uploaded images from images table */
/* 2.7 allow users to navigate to one content item */
/* 2.8 navigate pages by way of their association */
/* 3.1 Search for specific pages by keyword */
/* 3.2 search for specific pages by keyword while limiting search categories */

/* get sanitized sorting, type filters, search option from URL */
$sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$type_filter = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$conditions = [];
$params = [];

/* filter by type, checks if values are empty */
if (!empty($type_filter)) {
    $conditions[] = "(pokemon.type1 = :type_filter OR pokemon.type2 = :type_filter)";
    /*uses prepared statments and bound parameters */
    $params[':type_filter'] = $type_filter;
}

/* search by keyword, checks if values are empty */
if (!empty($search)) {
    $conditions[] = "(pokemon.name LIKE :search
                    OR pokemon.description LIKE :search
                    OR pokemon.type1 LIKE :search
                    OR pokemon.type2 LIKE :search)";
    /*uses prepared statments and bound parameters */
    $params[':search'] = '%' . $search . '%';
}

$where_clause = '';

if (!empty($conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $conditions);
}

/* choose sort order at database level */
$order_by = "pokemon.name ASC";
$current_sort = "Name (A-Z)";

if ($sort === 'name_desc') {
    $order_by = "pokemon.name DESC";
    $current_sort = "Name (Z-A)";
} elseif ($sort === 'created_desc') {
    $order_by = "pokemon.created_at DESC";
    $current_sort = "Newest to Oldest";
} elseif ($sort === 'created_asc') {
    $order_by = "pokemon.created_at ASC";
    $current_sort = "Oldest to Newest";
} elseif ($sort === 'updated_desc') {
    $order_by = "pokemon.updated_at DESC";
    $current_sort = "Recently Updated";
} elseif ($sort === 'updated_asc') {
    $order_by = "pokemon.updated_at ASC";
    $current_sort = "Least Recently Updated";
}

$query = "
    SELECT pokemon.*, images.file_name, types.color AS type_color
    FROM pokemon
    LEFT JOIN images ON pokemon.id = images.pokemon_id
    LEFT JOIN types ON pokemon.type1 = types.name
    $where_clause
    ORDER BY $order_by
";

$statement = $db->prepare($query);

foreach ($params as $key => $value) {
    $statement->bindValue($key, $value);
}

$statement->execute();

$type_query = "SELECT name FROM types ORDER BY name ASC";
$type_statement = $db->prepare($type_query);
$type_statement->execute();
$types = $type_statement->fetchAll();

/* query one random Pokémon for featured section */
$featured_query = "
    SELECT pokemon.*, images.file_name
    FROM pokemon
    LEFT JOIN images ON pokemon.id = images.pokemon_id
    ORDER BY RAND()
    LIMIT 1
";
$featured_statement = $db->prepare($featured_query);
$featured_statement->execute();
$featured_pokemon = $featured_statement->fetch();
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

            <form method="get" action="pokedex.php" class="search-group">
                <label for="search">Search</label>
                <input 
                    id="search" 
                    type="text" 
                    name="search" 
                    value="<?= htmlspecialchars($search ?? '') ?>"
                    placeholder="Search Pokémon..."
                >

                <label for="type">Category</label>
                <select name="type" id="type">
                    <option value="">All Categories</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= htmlspecialchars($type['name']) ?>"
                            <?= ($type_filter === $type['name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (!empty($sort)): ?>
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                <?php endif; ?>

                <button type="submit">Search</button>
            </form>
        </header>

        <div id="mainheading">
            <h1>Kowski Komics Local Pokédex</h1>
            <h2><i>Browse all Pokémon in the database</i></h2>

            <!-- if user is logged in and is admin, show admin dashboard section -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
            <div class="admin-dashboard-row">
                <article id="admin-dashboard" class="admin-card">
                    <h3>Admin Dashboard</h3>
                    <p>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
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
        </div>

        <div id="navrow">
            <nav aria-label="Main Navigation">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="pokedex.php">Browse Pokédex</a></li>
                    <li class="dropdown">
                        <a href="pokedex.php">Types ▾</a>

                        <ul class="dropdown-menu">
                            <?php foreach ($types as $type): ?>
                                <li>
                                    <a href="pokedex.php?type=<?= urlencode($type['name']) ?>">
                                        <?= htmlspecialchars($type['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div> 

        <aside>
            <?php if ($featured_pokemon): ?>
                <p>
                    <?php if (!empty($featured_pokemon['file_name'])): ?>
                        <img class="pokemon" src="uploads/<?= htmlspecialchars($featured_pokemon['file_name']) ?>" alt="<?= htmlspecialchars($featured_pokemon['name']) ?>">
                    <?php endif; ?>

                    <strong>Featured Pokemon:</strong><br>
                    <?= htmlspecialchars($featured_pokemon['name']) ?><br><br>
                    <?= htmlspecialchars($featured_pokemon['description']) ?>
                </p>
            <?php else: ?>
                <p>
                    <img class="pokemon" src="images/pokeball.png" alt="Pokeball Placeholder">
                    No Pokemon available yet.
                </p>
            <?php endif; ?>
        </aside>

        <section>
            <article id="content">
                <h3>Pokédex Entries</h3>

                <div id="middlelinks">
                    <ul>
                        <li><a href="pokedex.php<?= (!empty($search) || !empty($type_filter)) ? '?' : '' ?><?= !empty($search) ? 'search=' . urlencode($search) : '' ?><?= (!empty($search) && !empty($type_filter)) ? '&' : '' ?><?= !empty($type_filter) ? 'type=' . urlencode($type_filter) : '' ?>">Name A-Z</a></li>
                        <li><a href="pokedex.php?sort=name_desc<?= !empty($type_filter) ? '&type=' . urlencode($type_filter) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Name Z-A</a></li>
                        <li><a href="pokedex.php?sort=created_desc<?= !empty($type_filter) ? '&type=' . urlencode($type_filter) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Newest to Oldest</a></li>
                        <li><a href="pokedex.php?sort=created_asc<?= !empty($type_filter) ? '&type=' . urlencode($type_filter) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Oldest to Newest</a></li>
                        <li><a href="pokedex.php?sort=updated_desc<?= !empty($type_filter) ? '&type=' . urlencode($type_filter) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Recently Updated</a></li>
                        <li><a href="pokedex.php?sort=updated_asc<?= !empty($type_filter) ? '&type=' . urlencode($type_filter) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Least Recently Updated</a></li>
                    </ul>
                    <div class="current-filters">
                        <p>
                            <strong>Sort:</strong> <?= htmlspecialchars($current_sort) ?>
                        </p>

                        <p>
                            <strong>Type Category:</strong> <?= !empty($type_filter) ? htmlspecialchars($type_filter) : 'All Categories' ?>
                        </p>

                        <?php if (!empty($search)): ?>
                        <p>
                            <strong>Search:</strong> <?= htmlspecialchars($search) ?>
                        </p>
                        <?php endif; ?>
                    </div>

                    <!-- if Pokémon found, loop through and display each entry -->
                    <?php while ($pokemon = $statement->fetch()): ?>
                        <div class="imagecontent" style="background: linear-gradient(135deg, <?= htmlspecialchars($pokemon['type_color']) ?>, #ffffff); cursor: pointer;" onclick="window.location.href='pokemon.php?id=<?= $pokemon['id'] ?>'">

                            <?php if (!empty($pokemon['file_name'])): ?>
                                <img class="pokemon" src="uploads/<?= htmlspecialchars($pokemon['file_name']) ?>" alt="<?= htmlspecialchars($pokemon['name']) ?>">
                            <?php endif; ?>

                            <div>
                                <p class="p1">
                                    <a class="content-link" href="pokemon.php?id=<?= $pokemon['id'] ?>">
                                        #<?= $pokemon['pokedex_number'] ?> - <?= htmlspecialchars($pokemon['name']) ?>
                                    </a>
                                </p>

                                <p class="middle-paragraph">
                                    <strong>Type:</strong>
                                    <?= htmlspecialchars($pokemon['type1']) ?>
                                    <?= $pokemon['type2'] ? ' / ' . htmlspecialchars($pokemon['type2']) : '' ?>
                                </p>

                                <p class="middle-paragraph">
                                    <strong>Click to view all Pokemon information</strong>
                                </p>

                                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                                    <p class="content-link-wrap">
                                        <a class="content-link" href="edit_pokemon.php?id=<?= $pokemon['id'] ?>">Edit</a>
                                    </p>
                                <?php endif; ?>
                            </div>

                        </div>
                    <?php endwhile; ?>
            </article>
        </section>

        <footer>
            <nav aria-label="Footer Navigation">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="pokedex.php">Pokedex</a></li>
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