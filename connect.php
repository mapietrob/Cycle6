<?php
// Database configuration
$host = 'localhost';
$dbname = 'csci375fa23';
$username = 'csci375fa23';
$password = 'csci375fa23!';

try {
    // Create a PDO instance as db connection to your database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // throw exceptions for every error
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Session configuration for user authentication
    // Starting the session on every page that includes this file ensures
    // user session data is accessible application-wide
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

} catch(PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
}
?>