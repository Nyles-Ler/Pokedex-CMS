<!-- edit_pokemon.php - A page to edit an existing Pokémon entry in the database. Admin users only. -->

<!-- 2.2 update existing page/product -->

<!-- 6.1 add/update image via upload -->
<!-- images can be added or replaced when updating a page -->
<!-- uploads are optional -->
<!-- images are validated using file_is_an_image() -->
<!-- valid images are stored in uploads folder and saved in images table -->
<!-- invalid uploads are rejected -->

<!-- 6.2 remove image from page -->
<!-- existing image can be removed using delete checkbox -->
<!-- image file is deleted from uploads folder -->
<!-- image record is deleted from images table -->

<?php
require('authenticate.php');
require('db_connect.php');

require '\xampp\htdocs\Winter2026\Web Developement 2\C7 Starting Files\ImageResize.php';
require '\xampp\htdocs\Winter2026\Web Developement 2\C7 Starting Files\ImageResizeException.php';

function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
    $current_folder = dirname(__FILE__);
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $image_info = getimagesize($temporary_path);

    if ($image_info === false) {
        return false;
    }

    $actual_mime_type = $image_info['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

$edit_message = '';

if (isset($_GET['message'])):
    if ($_GET['message'] === 'updated'):
        $edit_message = "Pokemon updated successfully!";
    elseif ($_GET['message'] === 'error'):
        $edit_message = "Error while updating Pokemon!";
    endif;
endif;

$types_query = "SELECT name FROM types ORDER BY name ASC";

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
    $delete_image = filter_input(INPUT_POST, 'delete_image', FILTER_SANITIZE_NUMBER_INT);

    if ($type2 === ''):
        $type2 = null;
    endif;

    if ($height === '' || $height === null):
        $height = null;
    endif;

    if ($weight === '' || $weight === null):
        $weight = null;
    endif;

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
                  description = :description
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
    $statement->bindValue(':id', $id, PDO::PARAM_INT);

    if ($statement->execute()):

        $current_image_query = "SELECT id, file_name FROM images WHERE pokemon_id = :pokemon_id LIMIT 1";
        $current_image_statement = $db->prepare($current_image_query);
        $current_image_statement->bindValue(':pokemon_id', $id, PDO::PARAM_INT);
        $current_image_statement->execute();
        $current_image = $current_image_statement->fetch();

        /* 6.2 delete image if checkbox selected */
        if ($delete_image && $current_image):
            $old_path = file_upload_path($current_image['file_name'], 'uploads');

            if (file_exists($old_path)) {
                unlink($old_path);
            }

            $delete_query = "DELETE FROM images WHERE id = :id";
            $delete_statement = $db->prepare($delete_query);
            $delete_statement->bindValue(':id', $current_image['id'], PDO::PARAM_INT);
            $delete_statement->execute();

            $current_image = false;
        endif;

        /* 6.1 check if new image uploaded */
        $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

        if ($image_upload_detected):
            $image_filename = $_FILES['image']['name'];
            $temporary_path = $_FILES['image']['tmp_name'];

            $safe_filename = uniqid() . '-' . basename($image_filename);
            $new_image_path = file_upload_path($safe_filename, 'uploads');

            /* validate image */
            if (file_is_an_image($temporary_path, $new_image_path)):

                /* move image to uploads folder */
                if (move_uploaded_file($temporary_path, $new_image_path)):

                    try {
                        /* resize image using Gumlet */
                        $resized_image = new Gumlet\ImageResize($new_image_path);
                        $resized_image->resizeToWidth(400);
                        $resized_image->save($new_image_path);

                        /* replace old image if one exists */
                        if ($current_image):
                            $old_path = file_upload_path($current_image['file_name'], 'uploads');

                            if (file_exists($old_path)) {
                                unlink($old_path);
                            }

                            $old_delete_query = "DELETE FROM images WHERE id = :id";
                            $old_delete_statement = $db->prepare($old_delete_query);
                            $old_delete_statement->bindValue(':id', $current_image['id'], PDO::PARAM_INT);
                            $old_delete_statement->execute();
                        endif;

                        $image_query = "INSERT INTO images (pokemon_id, file_name)
                                        VALUES (:pokemon_id, :file_name)";
                        $image_statement = $db->prepare($image_query);
                        $image_statement->bindValue(':pokemon_id', $id, PDO::PARAM_INT);
                        $image_statement->bindValue(':file_name', $safe_filename);
                        $image_statement->execute();

                    } catch (Gumlet\ImageResizeException $e) {
                        header("Location: edit_pokemon.php?id={$id}&message=error");
                        exit;
                    }

                else:
                    header("Location: edit_pokemon.php?id={$id}&message=error");
                    exit;
                endif;

            else:
                header("Location: edit_pokemon.php?id={$id}&message=error");
                exit;
            endif;
        endif;

        header("Location: edit_pokemon.php?id={$id}&message=updated");
    else:
        header("Location: edit_pokemon.php?id={$id}&message=error");
    endif;
    exit;

elseif (isset($_GET['id'])):

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT pokemon.*, images.id AS image_id, images.file_name
              FROM pokemon
              LEFT JOIN images ON pokemon.id = images.pokemon_id
              WHERE pokemon.id = :id
              LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $pokemon = $statement->fetch();

else:
    $id = false;
endif;

$types_statement = $db->prepare($types_query);
$types_statement->execute();

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
                <h1>Edit Pokémon</h1>
                <h2><i>Update an existing Pokémon entry</i></h2>
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

                    <!-- Display message -->
                    <?php if ($edit_message): ?>
                        <p class="middle-paragraph"><?= htmlspecialchars($edit_message) ?></p>
                    <?php endif; ?>

                    <p class="p1"><strong>Select a Pokémon</strong></p>

                    <ul class="pokemon-list">
                        <?php while ($pokemon_row = $list_statement->fetch()): ?>
                            <li>
                                <p class="content-link-wrap">
                                    <a class="content-link" href="edit_pokemon.php?id=<?= $pokemon_row['id'] ?>">
                                        #<?= $pokemon_row['pokedex_number'] ?> - <?= htmlspecialchars($pokemon_row['name']) ?>
                                    </a>
                                </p>
                            </li>
                        <?php endwhile; ?>
                    </ul>

                    <?php if ($id && $pokemon): ?>

                        <p class="p1"><strong>Editing: <?= htmlspecialchars($pokemon['name']) ?></strong></p>

                        <form method="post" action="edit_pokemon.php" enctype="multipart/form-data">

                            <input type="hidden" name="id" value="<?= $pokemon['id'] ?>">

                            <label for="pokedex_number">Pokédex Number</label>
                            <input id="pokedex_number" name="pokedex_number" type="number" value="<?= $pokemon['pokedex_number'] ?>" required>

                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" value="<?= htmlspecialchars($pokemon['name']) ?>" required>

                            <label for="type1">Primary Type</label>
                            <select id="type1" name="type1" required>
                                <?php while ($type = $types_statement->fetch()): ?>
                                    <option value="<?= $type['name'] ?>" <?= ($pokemon['type1'] === $type['name']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['name']) ?>
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
                                        <?= htmlspecialchars($type['name']) ?>
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
                            <textarea id="description" name="description" required><?= htmlspecialchars($pokemon['description']) ?></textarea>

                            <label for="image">Upload New Image (optional)</label>
                            <input id="image" name="image" type="file">

                            <?php if (!empty($pokemon['file_name'])): ?>

                                <!-- styled current image -->
                                <div class="imagecontent">
                                    <img class="pokemon"
                                         src="uploads/<?= htmlspecialchars($pokemon['file_name']) ?>"
                                         alt="<?= htmlspecialchars($pokemon['name']) ?>">

                                    <div>
                                        <p><strong>Current Image</strong></p>

                                        <label for="delete_image">
                                            <input id="delete_image" name="delete_image" type="checkbox" value="1">
                                            Delete current image
                                        </label>
                                    </div>
                                </div>

                            <?php endif; ?>

                            <input type="submit" value="Update Pokémon">

                        </form>

                    <?php else: ?>

                        <p class="middle-paragraph">Select a Pokémon from the list above to edit it.</p>

                    <?php endif; ?>

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