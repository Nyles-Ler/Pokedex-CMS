/* authenticate.php - Checks if user is logged in and is an admin. */
<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'):
    header('Location: index.php');
    exit;
endif;
?>