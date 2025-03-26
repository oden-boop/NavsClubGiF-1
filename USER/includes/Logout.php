<?php
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session
header("Location: /NavsClubGIF/NavsClubGIF/LOGIN/LoginAccount.php"); // Redirect to login page
exit();
?>
