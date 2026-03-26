/* logout.php - A simple page to log out the user by destroying the session
and redirecting to the homepage. */

<?php
session_start();
session_destroy();

header('Location: index.php');
exit;