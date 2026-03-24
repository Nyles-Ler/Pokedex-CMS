<?php
require('db_connect.php');

$username = 'serveruser';
$password = password_hash('gorgonzola7!', PASSWORD_DEFAULT);
$role = 'admin';

$query = "INSERT INTO users (username, password, role) 
          VALUES ('$username', '$password', '$role')";

$statement = $db->prepare($query);
$statement->bindValue(':username', $username);
$statement->bindValue(':password', $password);
$statement->bindValue(':role', $role);
$statement->execute();

echo "Admin user created successfully.";
?>