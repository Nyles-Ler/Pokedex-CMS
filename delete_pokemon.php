<?php
require('authenticate.php');
require('db_connect.php');

if ($_POST && isset($_POST['id'])):

    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "DELETE FROM pokemon WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    $message = "Pokémon deleted successfully.";

endif;

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

		    <?php if (isset($message)): ?>
		        <p><?= $message ?></p>
		    <?php endif; ?>

		    <ul class="pokemon-list">
		        <?php while ($pokemon = $list_statement->fetch()): ?>
		            <li>
		                #<?= $pokemon['pokedex_number'] ?> - <?= $pokemon['name'] ?>
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