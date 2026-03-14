<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'placefy_db');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_PORT', '3306');

$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}
?>
