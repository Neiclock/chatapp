<?php
//db_connect.php
// Database connection parameters
$host = 'localhost'; // Docker service name as the host
$username = 'user'; // As defined in your Docker config
$password = 'password'; // As defined in your Docker config
$db_name = 'chat_db'; // As defined in your Docker config

// Create a MySQLi database connection
$connection = new mysqli($host, $username, $password, $db_name);

// Check the connection
if ($connection->connect_error) {
    // Log the error instead of displaying it
    error_log("Connection failed: " . $connection->connect_error);
    die("Connection failed. Please contact the administrator.");
}

// Return the connection object
return $connection;
?>
