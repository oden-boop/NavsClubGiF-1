<?php
// ✅ Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: NewnavsClub-GIF/LOGIN/LoginAccount.php"); // Corrected path
    exit();
}
?>
