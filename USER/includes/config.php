<?php
// Enable error reporting for debugging (Disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$serverName = "localhost";
$dBUsername = "root";
$dBPassword = "";
$dBName = "navsclubs";

// Establish connection using MySQLi
$conn = new mysqli($serverName, $dBUsername, $dBPassword, $dBName);

// Check for connection errors
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set character encoding to avoid issues with special characters
if (!$conn->set_charset("utf8mb4")) {
    die("Error setting charset: " . $conn->error);
}

// Function to check if user is authenticated
function isAuthenticated() {
    return isset($_SESSION['usersid']); // Ensure 'usersid' is set
}

// Redirect user if not logged in
if (!isAuthenticated()) {
    header("Location: /NavsClubGIF/NavsClubGIF/LOGIN/LoginAccount.php");
    exit();
}

?>
