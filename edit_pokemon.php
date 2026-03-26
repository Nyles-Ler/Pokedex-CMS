/* edit_pokemon.php - A page to edit an existing Pokémon entry in the database. Admin users only. */

<?php
require('authenticate.php');
require('db_connect.php');

/* query types for dropdowns */
$types_query = "SELECT name FROM types ORDER BY name ASC";

/* if form was submitted, validate and update Pokémon */
if ($_POST
    && isset($_POST['id'])
    && !empty($_POST['pokedex_number'])
    && !empty($_POST['name'])
    && !empty($_POST['type1'])
    && !empty($_POST['hp'])
    && !empty($_POST['attack_stat'])
    && !empty($_POST['defense_stat'])
    && !empty($_POST['special_attack'])
    && !empty($_POST['special_defense'])
    && !empty($_POST['speed_stat'])
    && !empty($_POST['description'])):

    /* sanitize input */
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $pokedex_number = filter_input(INPUT_POST, 'pokedex_number', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $type1 = filter_input(INPUT_POST, 'type1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $type2 = filter_input(INPUT_POST, 'type2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $hp = filter_input(INPUT_POST, 'hp', FILTER_SANITIZE_NUMBER_INT);
    $attack_stat = filter_input(INPUT_POST, 'attack_stat', FILTER_SANITIZE_NUMBER_INT);
    $defense_stat = filter_input(INPUT_POST, 'defense_stat', FILTER_SANITIZE_NUMBER_INT);
    $special_attack = filter_input(INPUT_POST, 'special_attack', FILTER_SANITIZE_NUMBER_INT);
    $special_defense = filter_input(INPUT_POST, 'special_defense', FILTER_SANITIZE_NUMBER_INT);
    $speed_stat = filter_input(INPUT_POST, 'speed_stat', FILTER_SANITIZE_NUMBER_INT);
    $height = filter_input(INPUT_POST, 'height', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $weight = filter_input(INPUT_POST, 'weight', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    /* handle optional fields */
    if ($type2 === ''):
        $type2 = null;
    endif;

    if ($height === '' || $height === null):
        $height = null;
    endif;

    if ($weight === '' || $weight === null):
        $weight = null;
    endif;

    if ($image === ''):
        $image = null;
    endif;

    /* update Pokémon in database */
    $query = "UPDATE pokemon
              SET pokedex_number = :pokedex_number,
                  name = :name,
                  type1 = :type1,
                  type2 = :type2,
                  hp = :hp,
                  attack_stat = :attack_stat,
                  defense_stat = :defense_stat,
                  special_attack = :special_attack,
                  special_defense = :special_defense,
                  speed_stat = :speed_stat,
                  height = :height,
                  weight = :weight,
                  description = :description,
                  image = :image
              WHERE id = :id";

    $statement = $db->prepare($query);
    $statement->bindValue(':pokedex_number', $pokedex_number, PDO::PARAM_INT);
    $statement->bindValue(':name', $name);
    $statement->bindValue(':type1', $type1);
    $statement->bindValue(':type2', $type2);
    $statement->bindValue(':hp', $hp, PDO::PARAM_INT);
    $statement->bindValue(':attack_stat', $attack_stat, PDO::PARAM_INT);
    $statement->bindValue(':defense_stat', $defense_stat, PDO::PARAM_INT);
    $statement->bindValue(':special_attack', $special_attack, PDO::PARAM_INT);
    $statement->bindValue(':special_defense', $special_defense, PDO::PARAM_INT);
    $statement->bindValue(':speed_stat', $speed_stat, PDO::PARAM_INT);
    $statement->bindValue(':height', $height);
    $statement->bindValue(':weight', $weight);
    $statement->bindValue(':description', $description);
    $statement->bindValue(':image', $image);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    /* redirect back to edit page with success message */
    header("Location: edit_pokemon.php?id={$id}");
    exit;

/* if form not submitted but id is in query string, fetch Pokémon to edit */
elseif (isset($_GET['id'])):

    /* sanitize input */
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    /* query Pokémon from database */
    $query = "SELECT * FROM pokemon WHERE id = :id LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    /* fetch Pokémon to edit */
    $pokemon = $statement->fetch();

else:
    $id = false;
endif;

$types_statement = $db->prepare($types_query);
$types_statement->execute();

/* query Pokémon to display list */
$list_query = "SELECT id, pokedex_number, name FROM pokemon ORDER BY pokedex_number ASC";
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
    <title>Update Pokémon Entry - Kowski Komics</title>
</head>
	<body>
		<main>
		    <h1>Edit Pokémon</h1>

		    <h2>Select a Pokémon</h2>
		    <ul class="pokemon-list">
                /* loop through Pokémon and display with edit link */
		        <?php while ($pokemon_row = $list_statement->fetch()): ?>
		            <li>
                        /* show Pokémon name and link to edit page */
		                <a href="edit_pokemon.php?id=<?= $pokemon_row['id'] ?>">
		                    #<?= $pokemon_row['pokedex_number'] ?> - <?= $pokemon_row['name'] ?>
		                </a>
		            </li>
		        <?php endwhile; ?>
		    </ul>
            /* if Pokémon selected, show edit form pre-filled with Pokémon data */
		    <?php if ($id && $pokemon): ?>
		        <form method="post" action="edit_pokemon.php">
		            <input type="hidden" name="id" value="<?= $pokemon['id'] ?>">

		            <label for="pokedex_number">Pokédex Number</label>
		            <input id="pokedex_number" name="pokedex_number" type="number" value="<?= $pokemon['pokedex_number'] ?>" required>

		            <label for="name">Name</label>
		            <input id="name" name="name" type="text" value="<?= $pokemon['name'] ?>" required>

		            <label for="type1">Primary Type</label>
		            <select id="type1" name="type1" required>
		                <?php while ($type = $types_statement->fetch()): ?>
		                    <option value="<?= $type['name'] ?>" <?= ($pokemon['type1'] === $type['name']) ? 'selected' : '' ?>>
		                        <?= $type['name'] ?>
		                    </option>
		                <?php endwhile; ?>
		            </select>

		            <?php
		            $types_statement = $db->prepare($types_query);
		            $types_statement->execute();
		            ?>

		            <label for="type2">Secondary Type</label>
		            <select id="type2" name="type2">
		                <option value="">No secondary type</option>
		                <?php while ($type = $types_statement->fetch()): ?>
		                    <option value="<?= $type['name'] ?>" <?= ($pokemon['type2'] === $type['name']) ? 'selected' : '' ?>>
		                        <?= $type['name'] ?>
		                    </option>
		                <?php endwhile; ?>
		            </select>

		            <label for="hp">HP</label>
		            <input id="hp" name="hp" type="number" value="<?= $pokemon['hp'] ?>" required>

		            <label for="attack_stat">Attack</label>
		            <input id="attack_stat" name="attack_stat" type="number" value="<?= $pokemon['attack_stat'] ?>" required>

		            <label for="defense_stat">Defense</label>
		            <input id="defense_stat" name="defense_stat" type="number" value="<?= $pokemon['defense_stat'] ?>" required>

		            <label for="special_attack">Special Attack</label>
		            <input id="special_attack" name="special_attack" type="number" value="<?= $pokemon['special_attack'] ?>" required>

		            <label for="special_defense">Special Defense</label>
		            <input id="special_defense" name="special_defense" type="number" value="<?= $pokemon['special_defense'] ?>" required>

		            <label for="speed_stat">Speed</label>
		            <input id="speed_stat" name="speed_stat" type="number" value="<?= $pokemon['speed_stat'] ?>" required>

		            <label for="height">Height</label>
		            <input id="height" name="height" type="number" step="0.01" value="<?= $pokemon['height'] ?>">

		            <label for="weight">Weight</label>
		            <input id="weight" name="weight" type="number" step="0.01" value="<?= $pokemon['weight'] ?>">

		            <label for="description">Description</label>
		            <textarea id="description" name="description" required><?= $pokemon['description'] ?></textarea>

		            <label for="image">Image Path</label>
		            <input id="image" name="image" type="text" value="<?= $pokemon['image'] ?>">

		            <input type="submit" value="Update Pokémon">
		        </form>
		    <?php else: ?>
		        <p>Select a Pokémon from the list above to edit it.</p>
		    <?php endif; ?>

		    <p class="content-link-wrap"><a class="content-link" href="index.php#admin-dashboard">Back to Dashboard</a></p>
		</main>
	</body>
</html>