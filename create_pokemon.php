/* create_pokemon.php - A form to create a new Pokémon entry in the database.
Validates input and displays a success message. */

<?php
/* admin users only */
require('authenticate.php');
require('db_connect.php');

/* get the types for the dropdowns */
$types_query = "SELECT name FROM types ORDER BY name ASC";
$types_statement = $db->prepare($types_query);
$types_statement->execute();

/* if form was submitted, validate and insert new Pokémon */
if ($_POST
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

    /* insert new Pokémon into database */
    $query = "INSERT INTO pokemon
                (pokedex_number, name, type1, type2, hp, attack_stat, defense_stat,
                 special_attack, special_defense, speed_stat, height, weight, description, image)
              VALUES
                (:pokedex_number, :name, :type1, :type2, :hp, :attack_stat, :defense_stat,
                 :special_attack, :special_defense, :speed_stat, :height, :weight, :description, :image)";

    /* prepare and execute query */
    $statement = $db->prepare($query);
    /* bind values */
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
    $statement->execute();

    /* set success message */
    $message = "Pokémon created successfully.";

    /* re-query types for the dropdowns */
    $types_statement = $db->prepare($types_query);
    $types_statement->execute();

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
    <title>Create Pokémon Entry - Kowski Komics</title>
</head>
	<body>
		<main>
		    <h1>Create Pokémon</h1>

            /* display success message if set */
		    <?php if (isset($message)): ?>
		        <p><?= $message ?></p>
		    <?php endif; ?>

		    <form method="post" action="create_pokemon.php">
		        <label for="pokedex_number">Pokédex Number</label>
		        <input id="pokedex_number" name="pokedex_number" type="number" required>

		        <label for="name">Name</label>
		        <input id="name" name="name" type="text" required>

		        <label for="type1">Primary Type</label>
		        <select id="type1" name="type1" required>
		            <option value="">Select a type</option>
                    /* show types in dropdown */
		            <?php while ($type = $types_statement->fetch()): ?>
                        /* show type as option in dropdown */
		                <option value="<?= $type['name'] ?>"><?= $type['name'] ?></option>
		            <?php endwhile; ?>
		        </select>

		        <?php
		        $types_statement = $db->prepare($types_query);
		        $types_statement->execute();
		        ?>

		        <label for="type2">Secondary Type</label>
		        <select id="type2" name="type2">
		            <option value="">No secondary type</option>
                    /* show types in dropdown */
		            <?php while ($type = $types_statement->fetch()): ?>
		                <option value="<?= $type['name'] ?>"><?= $type['name'] ?></option>
		            <?php endwhile; ?>
		        </select>

		        <label for="hp">HP</label>
		        <input id="hp" name="hp" type="number" required>

		        <label for="attack_stat">Attack</label>
		        <input id="attack_stat" name="attack_stat" type="number" required>

		        <label for="defense_stat">Defense</label>
		        <input id="defense_stat" name="defense_stat" type="number" required>

		        <label for="special_attack">Special Attack</label>
		        <input id="special_attack" name="special_attack" type="number" required>

		        <label for="special_defense">Special Defense</label>
		        <input id="special_defense" name="special_defense" type="number" required>

		        <label for="speed_stat">Speed</label>
		        <input id="speed_stat" name="speed_stat" type="number" required>

		        <label for="height">Height</label>
		        <input id="height" name="height" type="number" step="0.01">

		        <label for="weight">Weight</label>
		        <input id="weight" name="weight" type="number" step="0.01">

		        <label for="description">Description</label>
		        <textarea id="description" name="description" required></textarea>

		        <label for="image">Image Path</label>
		        <input id="image" name="image" type="text" placeholder="images/pikachu.png">

		        <input type="submit" value="Create Pokémon">
		    </form>

		    <p class="content-link-wrap"><a class="content-link" href="index.php#admin-dashboard">Back to Dashboard</a></p>
		</main>
	</body>
</html>