<!-- create_pokemon.php - A form to create a new Pokémon entry in the database.
Validates input and displays a success message. -->

<!-- 2.1 create new page/product -->
<!-- 6.1 add image to page via upload -->
<!-- images are optional when creating a page -->
<!-- uploaded images are validated using file_is_an_image() -->
<!-- valid images are moved to uploads folder -->
<!-- filename is stored in images table -->
<!-- invalid uploads are rejected and not saved -->

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

/* test image-ness */
function file_is_an_image($temporary_path, $new_path) {
    /* checks file extension and actual file type,prevents fake images */
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

/* set upload messages */
$image_message = '';
$pokemon_message = '';

/* get the types for the dropdowns */
$types_query = "SELECT name FROM types ORDER BY name ASC";
$types_statement = $db->prepare($types_query);
$types_statement->execute();

/* checks if form was submitted using POST and required inputs are filled */
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

    /* sanitize all inputs, prevents injecting scripts XSS */
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

    /* non required inputs into NULL */
    if ($type2 === ''):
        $type2 = null;
    endif;

    if ($height === '' || $height === null):
        $height = null;
    endif;

    if ($weight === '' || $weight === null):
        $weight = null;
    endif;

    $query = "INSERT INTO pokemon
                (pokedex_number, name, type1, type2, hp, attack_stat, defense_stat,
                 special_attack, special_defense, speed_stat, height, weight, description)
              VALUES
                (:pokedex_number, :name, :type1, :type2, :hp, :attack_stat, :defense_stat,
                 :special_attack, :special_defense, :speed_stat, :height, :weight, :description)";

    /* prepared statements and value binding safely inserts into db, prevents SQL injection */
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

    if ($statement->execute()):
        $pokemon_id = $db->lastInsertId();
        $pokemon_message = "Pokemon entered successfully!";

        /* checks if theres an image, only run validation if a file is uploaded */
        $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

        /* check if image was uploaded (optional upload) */
        if ($image_upload_detected):

            $image_filename = $_FILES['image']['name'];
            $temporary_path = $_FILES['image']['tmp_name'];

             /* create safe filename and upload path */
            $safe_filename = uniqid() . '-' . basename($image_filename);
            $new_image_path = file_upload_path($safe_filename, 'uploads');

            /* 6.1 validate image using "image-ness" test */
            if (file_is_an_image($temporary_path, $new_image_path)):

                /* move file to uploads folder */
                if (move_uploaded_file($temporary_path, $new_image_path)):

                    try {
                        /* resize image using Gumlet (PHP resizing requirement) */
                        $resized_image = new Gumlet\ImageResize($new_image_path);
                        $resized_image->resizeToWidth(400);
                        $resized_image->save($new_image_path);

                        /* 6.1 insert filename into images table */
                        $image_query = "INSERT INTO images (pokemon_id, file_name)
                                        VALUES (:pokemon_id, :file_name)";
                        $image_statement = $db->prepare($image_query);
                        $image_statement->bindValue(':pokemon_id', $pokemon_id, PDO::PARAM_INT);
                        $image_statement->bindValue(':file_name', $safe_filename);
                        $image_statement->execute();

                        $image_message = "Image uploaded and resized successfully.";
                    } catch (Gumlet\ImageResizeException $e) {
                        $image_message = "Image uploaded, but resizing failed.";
                    }

                else:
                    $image_message = "There was a problem saving the image.";
                endif;

            else:
                $image_message = "Upload rejected (not a valid image).";
            endif;

        endif;

    else:
        $pokemon_message = "Pokemon failed to upload!";
    endif;

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
            <h1>Create Pokémon</h1>
            <h2><i>Add a new Pokémon entry to the database</i></h2>
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

                <?php if ($image_message): ?>
                    <p class="middle-paragraph"><?= htmlspecialchars($image_message) ?></p>
                <?php endif; ?>

                <?php if ($pokemon_message): ?>
                    <p class="middle-paragraph"><?= htmlspecialchars($pokemon_message) ?></p>
                <?php endif; ?>

                <p class="p1"><strong>Create a New Pokémon</strong></p>

                <form method="post" action="create_pokemon.php" enctype="multipart/form-data">

                    <label for="pokedex_number">Pokédex Number</label>
                    <input id="pokedex_number" name="pokedex_number" type="number" required>

                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" required>

                    <label for="type1">Primary Type</label>
                    <select id="type1" name="type1" required>
                        <option value="">Select a type</option>
                        <?php while ($type = $types_statement->fetch()): ?>
                            <option value="<?= $type['name'] ?>"><?= htmlspecialchars($type['name']) ?></option>
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
                            <option value="<?= $type['name'] ?>"><?= htmlspecialchars($type['name']) ?></option>
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

                    <label for="image">Upload Image (optional)</label>
                    <input id="image" name="image" type="file">

                    <input type="submit" value="Create Pokémon">
                </form>

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