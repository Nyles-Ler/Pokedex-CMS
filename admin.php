<!-- admin.php - Makes sure user is admin,
   then shows admin dashboard with links to admin functions -->
<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'):
    header('Location: index.php');
    exit;
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
    <title>Admin Dashboard - Kowski Komics</title>
</head>
<body>
    <main>
        <article id="admin-dashboard" class="admin-card">
            <h3>Admin Dashboard</h3>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>

            <nav aria-label="Admin Navigation">
                <ul class="admin-links">
                    <li><a href="create_pokemon.php">Create Pokémon</a></li>
                    <li><a href="edit_pokemon.php">Update Pokémon</a></li>
                    <li><a href="delete_pokemon.php">Delete Pokémon</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                </ul>
            </nav>
        </article>
    </main>
</body>
</html>