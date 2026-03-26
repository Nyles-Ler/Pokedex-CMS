/* delete_pokemon.php - A page to delete a Pokémon entry from the database. Admin users only. */

<?php
require('authenticate.php');
require('db_connect.php');

/* if form was submitted, delete Pokémon */
if ($_POST && isset($_POST['id'])):

    /* sanitize input */
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    /* delete Pokémon from database */
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
	<body>
		<main>
		    <h1>Delete Pokémon</h1>

            /* display success message if set */
		    <?php if (isset($message)): ?>
		        <p><?= $message ?></p>
		    <?php endif; ?>

		    <ul class="pokemon-list">
                /* loop through Pokémon and display with delete form */
		        <?php while ($pokemon = $list_statement->fetch()): ?>
		            <li>
                        /* show Pokémon name and types */
		                #<?= $pokemon['pokedex_number'] ?> - <?= $pokemon['name'] ?>
                        /* show types if they exist */
		                (<?= $pokemon['type1'] ?><?= $pokemon['type2'] ? ' / ' . $pokemon['type2'] : '' ?>)

		                <form method="post" action="delete_pokemon.php" style="display:inline;">
		                    <input type="hidden" name="id" value="<?= $pokemon['id'] ?>">
		                    <input type="submit" value="Delete">
		                </form>
		            </li>
		        <?php endwhile; ?>
		    </ul>

		    <p class="content-link-wrap"><a class="content-link" href="index.php#admin-dashboard">Back to Dashboard</a></p>
		</main>
	</body>
</html>