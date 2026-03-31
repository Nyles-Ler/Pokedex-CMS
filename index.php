<!-- index.php - The homepage of the Kowski Komics website.
Displays a welcome message, navigation links, and an admin dashboard for logged-in admin users. -->

<?php
session_start();
require('db_connect.php');

/* query to get all unique colors from types table for admin dashboard background */
$color_query = "SELECT color FROM types WHERE color IS NOT NULL";
$color_statement = $db->prepare($color_query);
$color_statement->execute();

$colors = [];

while ($row = $color_statement->fetch()):
    $colors[] = $row['color'];
endwhile;

/* create a gradient string from the colors or use a default gradient if no colors found */
$gradient = '';

if (!empty($colors)):
    $gradient = 'linear-gradient(135deg, ' . implode(', ', $colors) . ')';
else:
    $gradient = 'linear-gradient(135deg, #d9f99d, #a0e7e5)';
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
    <title>Kowski Komics</title>
</head>
	<body class="page-home">
		<main>
			<header>
				<img class="Kowskilogo" src="images/kowskilogo.jpg" alt="Kowski Komics Logo">
			
				<nav aria-label="User Navigation">
					<ul>
						<!-- show login/logout links based on session -->
						<?php if (isset($_SESSION['user_id'])): ?>
							<li><a href="logout.php">Log Out</a></li>
							<?php else: ?>
								<li><a href="login.php">Log In</a></li>
						<?php endif; ?>
						<li><a href="index.php">Home</a></li>
						<li><a href="forgot_password.php">Forgot Password</a></li>
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
				<h2><i>Discover your favourite Pokémon and learn more about them!</i></h2>

			<?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
	        <div class="admin-dashboard-row">
	            <article id="admin-dashboard" class="admin-card" style="background: <?= htmlspecialchars($gradient) ?>;">
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
			</div>

			<div id="navrow">
				<nav aria-label="Main Navigation">
					<ul>
						<li><a href="pokedex.php">Browse Pokédex</a></li>
						<li><a href="pokemontypes.php">Explore Pokémon Types</a></li>
					</ul>
				</nav>
			</div>

			<aside>
				<nav aria-label="Sidebar Navigation">
					<ul>
						<li><a href="under_maintenance.php">About Kowski Komics</a></li>
						<li><a href="under_maintenance.php">Store Events</a></li>
						<li><a href="under_maintenance.php">Meet the Staff</a></li>
						<li><a href="under_maintenance.php">News & Blog</a></li>
						<li><a href="under_maintenance.php">Frequently Asked Questions</a></li>
						<li><a href="under_maintenance.php">Join the Kowski Mailing List</a></li>
						<li><a href="contact.php">Contact Us</a></li>
					</ul>
				</nav>
				<p><img class="Kowskilogo" src="images/kowskilogo.jpg" alt="Kowski Logo">Welcome to Kowski Komics, Winnipeg’s 
					friendly neighbourhood comic shop and a fun destination for Pokémon fans of all ages. From comics and 
					collectibles to Pokédex favourites, we have something for every trainer.</p>
			</aside>
			
		<section>
			<article id="content">
  				<h3>Welcome to our homepage!</h3>
  				<p class="p1">Kowski Komics is a local comic and collectibles shop proudly serving the Winnipeg community.</p>
      			<p class="middle-paragraph">We offer comics, Pokémon merchandise, and a welcoming space for fans to explore, 
					learn, and connect. Browse our site to discover featured Pokémon, learn about different types, and find out 
					what is happening at the shop.</p>
      			
				<div id="imagecontent">
    				<img class="pikachu" src="images/pikachu.png" alt="Pikachu">
    				<p>Pikachu is an Electric-type Pokémon known for storing electricity in the pouches on its cheeks.
						When excited or threatened, it can release powerful electric attacks in a flash.</p>
    			</div>
				
				<p class="middle-paragraph">Kowski Komics was founded in 2010 with a passion for comics, collectibles, 
					and the vibrant fan community that brings them to life. Proudly serving the Winnipeg area, 
					our shop has become a welcoming space for fans of all ages to discover their favourite stories, 
					explore the world of Pokémon, and connect with others who share the same interests. 
					Whether you are a longtime collector or just getting started, Kowski Komics is here to celebrate 
					fandom and provide a fun, inclusive experience for everyone.</p>

    			<div id="middlelinks">
					<nav aria-label="Middle Navigation">
        				<ul>
          					<li><a href="kowskis_message.php">A message from Kowski</a></li>
          					<li><a href="under_maintenance.php">Upcoming Events</a></li>
          					<li><a href="under_maintenance.php">Meet our Team!</a></li>
        				</ul>
      				</nav>
				</div>
			</article>
		</section>

			<footer>
				<nav aria-label="Footer Navigation">
					<ul>
						<li><a href="index.php">Home</a></li>
						<li><a href="pokedex.php">Pokedex</a></li>
						<li><a href="pokemontypes.php">Pokemon Types</a></li>
						<li><a href="under_maintenance.php">Our Staff</a></li>
						<li><a href="contact.php">Contact Us</a></li>
					</ul>
				</nav>
				<p>Copyright &copy; 2026 Kowski Komics. All rights reserved.</p>
				<p class="p2">160 Princess Street, Winnipeg, MB R3B 1K9</p>
			</footer>
		</main>
	</body>
</html>