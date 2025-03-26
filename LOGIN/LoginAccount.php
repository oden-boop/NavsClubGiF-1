<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/GoogleLogin/vendor/autoload.php';
require_once __DIR__ . '/Components/config.php'; 

$client = new Google_Client();

// Ensure GOOGLE_CREDENTIALS_PATH is defined
if (!defined('GOOGLE_CREDENTIALS_PATH')) {
    define('GOOGLE_CREDENTIALS_PATH', __DIR__ . '/Components/newcredentials.json');
}

if (!file_exists(GOOGLE_CREDENTIALS_PATH)) {
    die("Error: newcredentials.json file not found at " . GOOGLE_CREDENTIALS_PATH);
}

$client->setAuthConfig(GOOGLE_CREDENTIALS_PATH);
$client->setRedirectUri('http://localhost/NavsClubGIF/NavsClubGIF/USER/HomePage.php');
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        die('Google OAuth Error: ' . htmlspecialchars($token['error']));
    }
    $client->setAccessToken($token);
    $_SESSION['access_token'] = $token;
    header('Location: login.php'); // Redirect to avoid URL issues
    exit;
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $_SESSION['access_token'] = $client->getAccessToken();
    }

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email = $google_account_info->email;
    $name = $google_account_info->name;
    $profile_picture = $google_account_info->picture;
}

// Logout function
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 25px;
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
            width: 100%;
            max-width: 250px;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
            margin-top: 15px;
        }
        .google-btn img {
            width: 20px;
            margin-right: 10px;
        }
        .google-btn:hover {
            background: #f4f4f4;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Login</h3>

    <?php if (isset($email)): ?>
        <p>Welcome, <?= htmlspecialchars($name) ?> (<?= htmlspecialchars($email) ?>)</p>
        <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture" style="border-radius: 50%; width: 100px;">
        <br><a href="?logout=true" class="btn btn-danger mt-3">Logout</a>
    <?php else: ?>
        <form action="Sign-inFunction.php" method="POST">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>

            <button type="submit" class="btn">Login</button>
        </form>

        <a href="<?= htmlspecialchars($client->createAuthUrl()) ?>" class="google-btn">
            <img src="google-icon.png" alt="Google Icon"> Sign in with Google
        </a>

        <a href="RegisterAccount.php" class="btn btn-link mt-3">Don't have an account? Sign Up</a>
    <?php endif; ?>
</div>

</body>
</html>
