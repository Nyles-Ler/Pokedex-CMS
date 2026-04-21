<!-- delete_pokemon.php - A page to delete a Pokémon entry from the database. Admin users only. -->

<?php
/* 2.2 only admin can delete page/product */
/* 6.2 remove associated image when page is deleted */
require('authenticate.php');
require('db_connect.php');

/* function for setting where uploaded files are stored */
function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
    $current_folder = dirname(__FILE__);
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

/* if form was submitted, delete Pokémon */
if ($_POST && isset($_POST['id'])):

    /* sanitize input, prevents SQL injection */
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    /* 6.2 get image linked to this page/product */
    $image_query = "SELECT id, file_name FROM images WHERE pokemon_id = :id LIMIT 1";
    $image_statement = $db->prepare($image_query);
    $image_statement->bindValue(':id', $id, PDO::PARAM_INT);
    $image_statement->execute();
    $image = $image_statement->fetch();

    /* 6.2 delete image file from uploads folder if it exists */
    if ($image):
        $image_path = file_upload_path($image['file_name'], 'uploads');

        if (file_exists($image_path)) {
            unlink($image_path);
        }

        /* 6.2 delete image record from images table */
        $delete_image_query = "DELETE FROM images WHERE id = :id";
        $delete_image_statement = $db->prepare($delete_image_query);
        $delete_image_statement->bindValue(':id', $image['id'], PDO::PARAM_INT);
        $delete_image_statement->execute();
    endif;

    /* 2.2 delete Pokémon from database */
    $query = "DELETE FROM pokemon WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    /* set success message */
    $message = "Pokémon deleted successfully.";

endif;

/* query Pokémon to display list */
$list_query = "SELECT id, pokedex_number, name, type1, type2 FROM pokemon ORDER BY pokedex_number ASC";
$list_statement = $db->prepare($list_query);
$list_statement->execute();
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
    <title>Delete Pokémon Entry - Kowski Komics</title>
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

		        <div class="search-group">
		            <label for="search">Search</label>
		            <input id="search" type="text">
		            <button type="submit">Search</button>
		        </div>
		    </header>

		    <div id="mainheading">
		        <h1>Delete Pokémon</h1>
		        <h2><i>Remove Pokémon from the database</i></h2>
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

		            <?php if (isset($message)): ?>
		                <p class="middle-paragraph"><?= htmlspecialchars($message) ?></p>
		            <?php endif; ?>

		            <p class="p1"><strong>Select a Pokémon to Delete</strong></p>

		            <ul class="pokemon-list">
		                <?php while ($pokemon = $list_statement->fetch()): ?>
		                    <li>
		                        <p class="content-link-wrap">
		                            <span class="content-link">
		                                #<?= $pokemon['pokedex_number'] ?> - <?= htmlspecialchars($pokemon['name']) ?>
		                            </span>
		                        </p>

		                        <p class="middle-paragraph">
		                            <?= htmlspecialchars($pokemon['type1']) ?>
		                            <?= $pokemon['type2'] ? ' / ' . htmlspecialchars($pokemon['type2']) : '' ?>
		                        </p>

		                        <form method="post" action="delete_pokemon.php">
		                            <input type="hidden" name="id" value="<?= $pokemon['id'] ?>">

		                            <input type="submit"
		                                   value="Delete Pokémon"
		                                   onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($pokemon['name']) ?>?');">
		                        </form>
		                    </li>
		                <?php endwhile; ?>
		            </ul>

		            <p class="content-link-wrap">
		                <a class="content-link" href="index.php">Back to Home</a>
		            </p>

		            <p class="content-link-wrap">
		                <a class="content-link" href="pokedex.php">Back to Pokédex</a>
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