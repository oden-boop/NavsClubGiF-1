<?php
// Prevent direct access to this file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    http_response_code(403);
    die("Access denied.");
}

// Define your NewsAPI key securely
define('NEWS_API_KEY', '14ae141ea8d5466485ba37118fa34aa7'); // Replace with your actual API key
?>
