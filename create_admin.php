/* create_admin.php - A script to create an admin user in the database. */

<?php
/* db connection */
require('db_connect.php');

/* set username for first admin user */
$username = 'serveruser';
/* set password for first admin user and hash it */
$password = password_hash('gorgonzola7!', PASSWORD_DEFAULT);
/* set role for first admin user */
$role = 'admin';

/* insert new admin user into users table */
$query = "INSERT INTO users (username, password, role) 
          VALUES ('$username', '$password', '$role')";

/* execute query */
$statement = $db->prepare($query);
$statement->bindValue(':username', $username);
$statement->bindValue(':password', $password);
$statement->bindValue(':role', $role);
$statement->execute();

/* display success message */
echo "Admin user created successfully.";
?>