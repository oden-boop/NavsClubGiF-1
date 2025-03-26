<?php
// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Google API Configuration
define('GOOGLE_CLIENT_ID', '570831625760-og8tdbge0i96ui6pgbstsgj3m9lb2mdg.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/NavsClubGIF/NavsClubGIF/USER/HomePage.php');

// ✅ Define the path to Google credentials JSON file
define('GOOGLE_CREDENTIALS_PATH', __DIR__ . '/../GoogleLogin/newcredentials.json');

// ✅ Ensure the credentials file exists before proceeding
if (!file_exists(GOOGLE_CREDENTIALS_PATH)) {
    die("Error: Google credentials file not found.");
}

// ✅ Set environment variable securely
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . GOOGLE_CREDENTIALS_PATH);

// Include Google Client Library
require_once __DIR__ . '/../GoogleLogin/vendor/autoload.php';

// ✅ Initialize Google Client
$googleClient = new Google_Client();
$googleClient->setClientId(GOOGLE_CLIaENT_ID);
$googleClient->setClientSecret(GOOGLE_CLIENT_SECRET);
$googleClient->setRedirectUri(GOOGLE_REDIRECT_URI);
$googleClient->setAuthConfig(GOOGLE_CREDENTIALS_PATH);
$googleClient->addScope(["email", "profile"]);

// Enable offline access to refresh tokens
$googleClient->setAccessType('offline');
$googleClient->setPrompt('select_account consent');

// ✅ Check and refresh access token if necessary
if (!empty($_SESSION['access_token'])) {
    $googleClient->setAccessToken($_SESSION['access_token']);

    // ✅ Refresh token if expired
    if ($googleClient->isAccessTokenExpired()) {
        $refreshToken = $googleClient->getRefreshToken();
        
        if ($refreshToken) {
            $newAccessToken = $googleClient->fetchAccessTokenWithRefreshToken($refreshToken);
            $_SESSION['access_token'] = $newAccessToken;
        } else {
            unset($_SESSION['access_token']); // Clear expired token
        }
    }
}
?>
