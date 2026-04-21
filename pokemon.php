<!-- pokemon.php - Dynamic page to display one Pokémon from the database. -->
<!-- 2.9 comment on specific pages -->
<!-- 2.5 View and moderate comments -->

<?php
session_start();
require('db_connect.php');

/* 2.7 allow users to navigate to one content item */
/* get Pokémon id from query string, page continues only if ID is valid */
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

/* if no id, go back to pokedex */
if (!$id) {
    header('Location: pokedex.php');
    exit;
}

/* 2.9 comment on specific pages */
$comment_error = '';
$comment_name = '';
/* 2.10 captcha verification */
$comment_text = '';
$captcha_input = '';

if ($_POST && !isset($_POST['moderate_comment'])) {
    /* added code */
    /* added if statement to check if there is user */
    if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
        $comment_name = $_SESSION['username'];
    } /* sanitze non logged in user */
    else {
        $comment_name = trim(filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    }

    $comment_text = trim(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $captcha_input = trim(filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    if ($comment_text === '' || $captcha_input === '') {
        $comment_error = 'Please enter a comment and CAPTCHA.';
    } elseif (!isset($_SESSION['user_id']) && $comment_name === '') {
        /* added error comment for non logged in user name */
        $comment_error = 'Please enter your name.';
    } elseif (
        !isset($_SESSION['captcha_text']) ||
        strtoupper($captcha_input) !== $_SESSION['captcha_text']
    ) {
        $comment_error = 'CAPTCHA was incorrect. Please try again.';
        unset($_SESSION['captcha_text']);
    } else {
        $insert_comment = "
            INSERT INTO comments (pokemon_id, user_name, comment)
            VALUES (:pokemon_id, :user_name, :comment)
        ";

        $comment_statement = $db->prepare($insert_comment);
        $comment_statement->bindValue(':pokemon_id', $id, PDO::PARAM_INT);
        $comment_statement->bindValue(':user_name', $comment_name);
        $comment_statement->bindValue(':comment', $comment_text);
        $comment_statement->execute();

        unset($_SESSION['captcha_text']);

        header("Location: pokemon.php?id=" . $id);
        exit;
    }
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $comments_query = "
        SELECT *
        FROM comments
        WHERE pokemon_id = :pokemon_id
        ORDER BY created_at DESC
    ";
} else {
    $comments_query = "
        SELECT *
        FROM comments
        WHERE pokemon_id = :pokemon_id
          AND is_hidden = 0
        ORDER BY created_at DESC
    ";
}

$comments_statement = $db->prepare($comments_query);
$comments_statement->bindValue(':pokemon_id', $id, PDO::PARAM_INT);
$comments_statement->execute();
$comments = $comments_statement->fetchAll();

/* 2.5 view and moderate comments, validates is POST and user is admin */
if (
    $_POST &&
    isset($_POST['moderate_comment']) &&
    isset($_SESSION['role']) &&
    $_SESSION['role'] === 'admin'
) {
    /* sanitzed moderation values */
    $comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if ($comment_id) {
        if ($action === 'hide') {
            $moderate_query = "UPDATE comments SET is_hidden = 1 WHERE id = :comment_id";
        } elseif ($action === 'unhide') {
            $moderate_query = "UPDATE comments SET is_hidden = 0 WHERE id = :comment_id";
        } elseif ($action === 'delete') {
            $moderate_query = "DELETE FROM comments WHERE id = :comment_id";
        }

        if (!empty($moderate_query)) {
            $moderate_statement = $db->prepare($moderate_query);
            $moderate_statement->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);
            $moderate_statement->execute();
        }
    }

    header("Location: pokemon.php?id=" . $id);
    exit;
}

/* query one Pokémon and its uploaded image */
$query = "
    SELECT pokemon.*, images.file_name, types.color AS type_color
    FROM pokemon
    LEFT JOIN images ON pokemon.id = images.pokemon_id
    LEFT JOIN types ON pokemon.type1 = types.name
    WHERE pokemon.id = :id
    LIMIT 1
";

$statement = $db->prepare($query);
$statement->bindValue(':id', $id, PDO::PARAM_INT);
$statement->execute();
$pokemon = $statement->fetch();

/* if Pokémon not found, go back */
if (!$pokemon) {
    header('Location: pokedex.php');
    exit;
}

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
    <title><?= htmlspecialchars($pokemon['name']) ?> - Pokédex</title>
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
            <h1>Kowski Komics Local Pokédex</h1>
            <h2><?= htmlspecialchars($pokemon['name']) ?></h2>
        </div>

        <div id="navrow">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
            <div class="admin-dashboard-row">
                <article id="admin-dashboard" class="admin-card">
                    <h3>Admin Dashboard</h3>
                    <p>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
                    <nav aria-label="Admin-Navigation">
                        <ul class="admin-links">
                            <li><a href="create_pokemon.php">Create Pokemon</a></li>
                            <li><a href="edit_pokemon.php?id=<?= $pokemon['id'] ?>">Update This Pokemon</a></li>
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
                <p>No Pokémon available yet.</p>
            <?php endif; ?>
        </aside>

        <section>
            <article id="content">
                <h3>Pokédex Entry</h3>

                <div class="imagecontent" style="background: linear-gradient(135deg, <?= htmlspecialchars($pokemon['type_color']) ?>, #ffffff);">

                    <?php if (!empty($pokemon['file_name'])): ?>
                        <img class="pokemon"
                             src="uploads/<?= htmlspecialchars($pokemon['file_name']) ?>"
                             alt="<?= htmlspecialchars($pokemon['name']) ?>">
                    <?php endif; ?>

                    <div>
                        <p class="pokemon-title">
                            <strong>#<?= $pokemon['pokedex_number'] ?> - <?= htmlspecialchars($pokemon['name']) ?></strong>
                        </p>

                        <p class="middle-paragraph">
                            <strong>Type:</strong>
                            <?= htmlspecialchars($pokemon['type1']) ?>
                            <?= $pokemon['type2'] ? ' / ' . htmlspecialchars($pokemon['type2']) : '' ?>
                        </p>

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

                        <p class="middle-paragraph">
                            <strong>Description:</strong> <?= htmlspecialchars($pokemon['description']) ?>
                        </p>
                    </div>

                </div>
                <div class="comments-section">
                    <h3>Comments</h3>

                    <form method="post" action="pokemon.php?id=<?= $id ?>" class="comment-form">
                        <!-- checks if user is logged in -->
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <label for="user_name">Your Name</label>
                            <input
                                type="text"
                                name="user_name"
                                id="user_name"
                                value="<?= htmlspecialchars($comment_name) ?>"
                                required
                            >
                            <!-- if logged in user -->
                        <?php else: ?>
                            <p class="middle-paragraph">
                                Comment username: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                            </p>
                        <?php endif; ?>

                        <label for="comment">Your Comment</label>
                        <textarea
                            name="comment"
                            id="comment"
                            rows="5"
                            required
                        ><?= htmlspecialchars($comment_text, ENT_QUOTES, 'UTF-8') ?></textarea>

                        <label for="captcha">Enter the CAPTCHA</label>
                        <img src="captcha.php?rand=<?= time() ?>" alt="CAPTCHA Image" class="captcha-image">

                        <input
                            type="text"
                            name="captcha"
                            id="captcha"
                            value=""
                            required
                        >

                        <p class="middle-paragraph">If the image is hard to read, refresh the page for a new CAPTCHA.</p>

                        <?php if (!empty($comment_error)): ?>
                        <p class="captcha-error"><?= htmlspecialchars($comment_error) ?></p>
                        <?php endif; ?>

                        <input type="submit" value="Post Comment">
                    </form>

                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-card">
                                <p>
                                    <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                                    <br>
                                    <small><?= htmlspecialchars($comment['created_at']) ?></small>
                                </p>

                                <?php if (!empty($comment['is_hidden'])): ?>
                                    <p class="middle-paragraph"><em>This comment is hidden by an admin.</em></p>
                                <?php else: ?>
                                    <p class="middle-paragraph">
                                        <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <form method="post" action="pokemon.php?id=<?= $id ?>" class="comment-moderation-form">
                                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                        <input type="hidden" name="moderate_comment" value="1">

                                        <?php if (!empty($comment['is_hidden'])): ?>
                                            <button type="submit" name="action" value="unhide">Unhide</button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="hide">Hide</button>
                                        <?php endif; ?>

                                        <button type="submit" name="action" value="delete" onclick="return confirm('Delete this comment?');">
                                            Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="middle-paragraph">No comments yet. Be the first to comment.</p>
                    <?php endif; ?>
                </div>

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