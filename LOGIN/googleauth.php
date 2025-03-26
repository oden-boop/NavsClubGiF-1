<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/GoogleLogin/vendor/autoload.php';
require_once __DIR__ . '/Components/config.php'; // Ensure this file defines GOOGLE_CREDENTIALS_PATH

$client = new Google_Client();

// Ensure GOOGLE_CREDENTIALS_PATH is defined
if (!defined('GOOGLE_CREDENTIALS_PATH')) {
    define('GOOGLE_CREDENTIALS_PATH', __DIR__ . '/Components/newcredentials.json');
}

// ✅ Check if credentials.json exists
if (!file_exists(GOOGLE_CREDENTIALS_PATH)) {
    die("Error: newcredentials.json file not found at " . GOOGLE_CREDENTIALS_PATH);
}

// Load Google credentials
$client->setAuthConfig(GOOGLE_CREDENTIALS_PATH);

// Set redirect URI (Ensure this matches your Google Developer Console setting)
$client->setRedirectUri('http://localhost/NavsClubGIF/NavsClubGIF/USER/HomePage.php');

// Set Google Scopes (Define what data you want to access)
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);

// Enable offline access (for refresh token)
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

// Handle Google OAuth login
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        die('Google OAuth Error: ' . htmlspecialchars($token['error']));
    }

    // Set and store access token
    $client->setAccessToken($token);
    $_SESSION['access_token'] = $token;

    // Redirect to avoid URL issues with ?code=
    header('Location: googleauth.php');
    exit;
}

// Check if the user is already authenticated
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);

    // ✅ Refresh token if expired
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $_SESSION['access_token'] = $client->getAccessToken();
    }

    // Get user information from Google
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    
    $email = $google_account_info->email;
    $name = $google_account_info->name;
    $profile_picture = $google_account_info->picture;

    echo "<p>Welcome, " . htmlspecialchars($name) . " ($email)</p>";
    echo "<img src='" . htmlspecialchars($profile_picture) . "' alt='Profile Picture' style='border-radius: 50%; width: 100px;'>";
    echo "<br><a href='?logout=true'>Logout</a>";
} else {
    // If the user is not authenticated, show the Google Login button
    echo "<a href='" . htmlspecialchars($client->createAuthUrl()) . "'>Google Login</a>";
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy(); // ✅ Properly destroy the session
    header('Location: googleauth.php');
    exit;
}
?>
